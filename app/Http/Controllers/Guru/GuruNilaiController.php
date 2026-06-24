<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\NilaiMapel;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Semester;
use App\Models\KelasAktif;
use App\Models\RiwayatKelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreNilaiRequest;
use App\Support\AcademicRecord;

/**
 * GuruNilaiController — Input & Kelola Nilai oleh Guru Mapel
 *
 * Fitur:
 * - List kelas & siswa per jadwal
 * - Input/update nilai (tugas1-5, UTS, UAS)
 * - Kalkulasi otomatis nilai akhir (40% tugas + 30% UTS + 30% UAS)
 * - Batas waktu edit nilai berdasarkan Semester::masihBisaEditNilai()
 * - Filter siswa remidi (nilai < 75)
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan fitur batas waktu edit nilai
 */
class GuruNilaiController extends Controller
{
    public function index()
    {
        $idGuru = session('id_guru');
        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $jadwal = JadwalMengajar::with([
                'kelasAktif.kelas',
                'mapel'
            ])
            ->where('id_guru', $idGuru)
            ->whereHas('kelasAktif', function ($q) use ($tahunAktif, $semesterAktif) {
                $q->where('id_tahun', $tahunAktif->id_tahun)
                    ->whereHas('semester', function ($semesterQuery) use ($semesterAktif) {
                        $semesterQuery->where('nama_semester', $semesterAktif->nama_semester);
                    });
            })
            ->get();

        return view(
            'guru.nilai.index_nilai',
            compact('jadwal')
        );
    }

    public function siswa($id)
{
    $jadwal = JadwalMengajar::with([
        'kelasAktif.kelas',
        'kelasAktif.tahunAjaran',
        'kelasAktif.semester',
        'mapel',
    ])->findOrFail($id);

    $detail = (object) [

        'nama_kelas' => $jadwal->kelasAktif->kelas->nama_kelas,

        'nama_mapel' => $jadwal->mapel->nama_mapel,

        'kode_mapel_semester' => AcademicRecord::kodeMapelSemester(
            $jadwal->mapel->id_mapel,
            $jadwal->kelasAktif->semester->nama_semester
        ),

        'id_kelas_aktif' => $jadwal->kelasAktif->id_kelas_aktif,

        'tahun' => $jadwal->kelasAktif->tahunAjaran->tahun,

        'nama_semester' => $jadwal->kelasAktif->semester->nama_semester,
    ];

    /*
    |--------------------------------------------------------------------------
    | CEK HAK EDIT
    |--------------------------------------------------------------------------
    */

    $canEdit =
        $jadwal->kelasAktif?->semester?->status === 'aktif'
        &&
        $jadwal->kelasAktif?->semester?->masihBisaEditNilai();

    /*
    |--------------------------------------------------------------------------
    | SISWA SESUAI KELAS AKTIF
    |--------------------------------------------------------------------------
    */

    $idSiswaKelas = SiswaKelas::where(
            'id_kelas_aktif',
            $jadwal->kelasAktif->id_kelas_aktif
        )
        ->pluck('id_siswa');

    $siswa = Siswa::whereIn(
            'id_siswa',
            $idSiswaKelas
        )
        ->orderBy('nama_lengkap')
        ->get()
        ->map(function ($siswa) use ($id) {

            $nilai = NilaiMapel::where(
                    'id_siswa',
                    $siswa->id_siswa
                )
                ->where(
                    'id_jadwal',
                    $id
                )
                ->first();

            $riwayat = RiwayatKelas::with([
                    'kelasAktif.kelas',
                    'kelasAktif.semester',
                    'kelasAktif.tahunAjaran',
                ])
                ->where(
                    'id_siswa',
                    $siswa->id_siswa
                )
                ->orderBy('id_kelas_aktif')
                ->get();

            return (object) [

                'id_siswa' => $siswa->id_siswa,

                'nama_lengkap' => $siswa->nama_lengkap,

                'nilai_akhir' => $nilai?->nilai_akhir,

                'riwayat_count' => $riwayat->count(),

                'riwayat_sebelumnya' => $riwayat,

            ];
        });

    if (request('remidi')) {

        $siswa = $siswa
            ->where('nilai_akhir', '<', 75)
            ->values();
    }

    return view(
        'guru.nilai.siswa',
        compact(
            'detail',
            'siswa',
            'id',
            'canEdit'
        )
    );
}

  public function input($id, $id_siswa)
{
    $jadwal = JadwalMengajar::with([
        'kelasAktif.kelas',
        'kelasAktif.semester',
        'mapel'
    ])->findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | BLOKIR SISWA YANG SUDAH LULUS
    |--------------------------------------------------------------------------
    */

    $sudahLulus = DB::table('kenaikan_kelas')
        ->where('id_siswa', $id_siswa)
        ->where('status', 'lulus')
        ->exists();

    if ($sudahLulus) {

        return redirect('guru/nilai/' . $id)
            ->with(
                'error',
                'Siswa yang sudah lulus tidak dapat diubah nilainya.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SEMENTARA TIDAK CEK BATAS EDIT NILAI
    |--------------------------------------------------------------------------
    |
    | Nanti setelah sistem selesai baru aktifkan lagi
    | validasi batas_edit_nilai.
    |
    */

    $detail = (object) [

        'nama_kelas' =>
            $jadwal->kelasAktif->kelas->nama_kelas,

        'nama_mapel' =>
            $jadwal->mapel->nama_mapel,

        'kode_mapel_semester' =>
            AcademicRecord::kodeMapelSemester(
                $jadwal->mapel->id_mapel,
                $jadwal->kelasAktif?->semester?->nama_semester
            ),

        'tahun' =>
            $jadwal->kelasAktif?->tahunAjaran?->tahun,

        'nama_semester' =>
            $jadwal->kelasAktif?->semester?->nama_semester,

    ];

    $siswa = Siswa::findOrFail($id_siswa);

    $nilai = NilaiMapel::where(
            'id_siswa',
            $id_siswa
        )
        ->where(
            'id_jadwal',
            $id
        )
        ->first();

    return view(
        'guru.nilai.input_nilai',
        [
            'detail'   => $detail,
            'siswa'    => $siswa,
            'nilai'    => $nilai,
            'jadwalId' => $id,
        ]
    );
}

    public function simpan(StoreNilaiRequest $request)
    {
        $id_siswa  = $request->id_siswa;
        $id_jadwal = $request->id_jadwal;

        $jadwal = JadwalMengajar::with([
            'kelasAktif.kelas'
        ])->findOrFail($id_jadwal);

        /*
        |--------------------------------------------------------------------------
        | BLOKIR KELAS XII
        |--------------------------------------------------------------------------
        */

        $namaKelas = strtoupper(
            $jadwal->kelasAktif?->kelas?->nama_kelas ?? ''
        );

        if (str_starts_with($namaKelas, 'XII')) {

            return redirect()->back()
                ->with(
                    'error',
                    'Kelas XII sudah lulus dan tidak dapat mengubah nilai.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | BLOKIR SISWA LULUS
        |--------------------------------------------------------------------------
        */

        $sudahLulus = DB::table('kenaikan_kelas')
            ->where('id_siswa', $id_siswa)
            ->where('status', 'lulus')
            ->exists();

        if ($sudahLulus) {

            return redirect()->back()
                ->with(
                    'error',
                    'Siswa yang sudah lulus tidak dapat diubah nilainya.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN NILAI
        |--------------------------------------------------------------------------
        */

        $data = $request->only([
            'tugas1',
            'tugas2',
            'tugas3',
            'tugas4',
            'tugas5',
            'uts',
            'uas'
        ]);

        $rataTugas =
            (
                ($data['tugas1'] ?? 0)
                + ($data['tugas2'] ?? 0)
                + ($data['tugas3'] ?? 0)
                + ($data['tugas4'] ?? 0)
                + ($data['tugas5'] ?? 0)
            ) / 5;

        $data['nilai_akhir'] = (int) round(
            ($rataTugas * 0.4)
            +
            (($data['uts'] ?? 0) * 0.3)
            +
            (($data['uas'] ?? 0) * 0.3)
        );

        NilaiMapel::updateOrCreate(

            [
                'id_siswa'  => $id_siswa,
                'id_jadwal' => $id_jadwal
            ],

            array_merge(
                $data,
                [
                    'id_nilai' => NilaiMapel::generateId()
                ]
            )

        );

        return redirect(
            'guru/nilai/' . $id_jadwal
        )->with(
            'success',
            'Nilai berhasil disimpan.'
        );
}

    public function detailNilai($id, $id_siswa)
    {
        $jadwal = JadwalMengajar::with([
            'kelasAktif.kelas',
            'kelasAktif.tahunAjaran',
            'kelasAktif.semester',
            'mapel',
        ])->findOrFail($id);

        $detail = (object) [
            'nama_kelas' => $jadwal->kelasAktif->kelas->nama_kelas,
            'nama_mapel' => $jadwal->mapel->nama_mapel,
            'kode_mapel_semester' => AcademicRecord::kodeMapelSemester(
                $jadwal->mapel->id_mapel,
                $jadwal->kelasAktif->semester->nama_semester
            ),
            'tahun' => $jadwal->kelasAktif->tahunAjaran->tahun,
            'nama_semester' => $jadwal->kelasAktif->semester->nama_semester,
        ];

        $siswa = Siswa::findOrFail($id_siswa);
        $riwayat = AcademicRecord::riwayatSiswa($id_siswa);

        $nilai = NilaiMapel::where('id_siswa', $id_siswa)
            ->where('id_jadwal', $id)
            ->first();

            
        return view('guru.nilai.detail_nilai_siswa', compact('detail', 'siswa', 'nilai', 'id', 'riwayat'));
    }

    public function riwayatNilai($id, $id_siswa)
{
    $jadwal = JadwalMengajar::with([
        'kelasAktif.kelas',
        'kelasAktif.semester',
        'kelasAktif.tahunAjaran',
        'mapel',
    ])->findOrFail($id);

    $siswa = Siswa::findOrFail($id_siswa);

    /*
    |--------------------------------------------------------------------------
    | Ambil riwayat kelas siswa selain kelas aktif saat ini
    |--------------------------------------------------------------------------
    */

    $riwayatKelas = RiwayatKelas::with([
        'kelasAktif.kelas',
        'kelasAktif.semester',
        'kelasAktif.tahunAjaran',
    ])
    ->where('id_siswa', $id_siswa)
    ->where('id_kelas_aktif', '!=', $jadwal->id_kelas_aktif)
    ->get();

    $idKelasAktif = $riwayatKelas
        ->pluck('id_kelas_aktif')
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | Ambil nilai mapel yang sama pada riwayat semester sebelumnya
    |--------------------------------------------------------------------------
    */

    $riwayatNilai = NilaiMapel::with([
        'jadwalMengajar.kelasAktif.kelas',
        'jadwalMengajar.kelasAktif.semester',
        'jadwalMengajar.kelasAktif.tahunAjaran',
        'jadwalMengajar.mapel',
    ])
    ->where('id_siswa', $id_siswa)

    ->whereHas('jadwalMengajar', function ($q) use (
        $jadwal,
        $idKelasAktif
    ) {

        $q->whereIn('id_kelas_aktif', $idKelasAktif)

          ->whereHas('mapel', function ($m) use ($jadwal) {

              $m->where(
                  'nama_mapel',
                  $jadwal->mapel->nama_mapel
              );

          });

    })

    ->get()

    ->sortByDesc(function ($nilai) {

        $tahun =
            $nilai->jadwalMengajar?->kelasAktif?->tahunAjaran?->tahun ?? '';

        $semester =
            $nilai->jadwalMengajar?->kelasAktif?->semester?->nama_semester === 'Genap'
            ? 2
            : 1;

        return $tahun . '-' . $semester;
    });

    return view(
        'guru.nilai.riwayat_nilai_siswa',
        compact(
            'jadwal',
            'siswa',
            'riwayatNilai'
        )
    );
}
}
