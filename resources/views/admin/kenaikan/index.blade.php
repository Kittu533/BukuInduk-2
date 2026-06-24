@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    KENAIKAN / KELULUSAN SISWA
</h3>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="alert alert-secondary">
    <strong>Urutan pakai:</strong>
    <ol class="mb-0 mt-2">
        <li>Aktifkan tahun ajaran yang sedang berjalan.</li>
        <li>Kalau semester aktif masih <strong>Ganjil</strong>, proses dulu semester baru di menu <strong>Semester</strong>.</li>
        <li>Setelah semester akhir selesai, pastikan siswa <strong>kelas X</strong> pada tahun ajaran aktif sudah terisi.</li>
        <li>Baru klik <strong>Proses Tahun Ajaran Baru</strong> di halaman ini.</li>
    </ol>
</div>

<div class="card table-card">
    

    <div class="card-body">

        <div class="alert alert-light border mb-3">
            <div class="fw-semibold">Konteks Akademik Aktif</div>
            <small class="text-muted">
                Tahun ajaran aktif:
                <strong>{{ $tahun->tahun }}</strong>
                |
                Semester aktif:
                <strong>{{ $semester->nama_semester }}</strong>
                |
                Target tahun ajaran baru:
                <strong>{{ $targetTahun?->tahun ?? 'Belum tersedia' }}</strong>
            </small>
        </div>

        @if(! $canProcessTahunBaru)
            @if(! $targetTahun)
                <div class="alert alert-danger">
                    Tahun ajaran berikutnya belum tersedia. Tambahkan dulu tahun ajaran baru sebelum memproses kenaikan kelas.
                </div>
            @elseif(! $gradeXIntakeReady)
                <div class="alert alert-danger">
                    Formasi <strong>kelas X</strong> belum lengkap. Isi dulu siswa baru/pindahan untuk rombel:
                    <strong>{{ $emptyGradeXClasses->join(', ') }}</strong>
                    sebelum kelas XII boleh diproses lulus lagi.
                </div>
            @endif
        @elseif($usesFallbackSemester)
            <div class="alert alert-warning">
                Sumber data rollover saat ini memakai semester <strong>{{ $sourceSemester->nama_semester }}</strong>, bukan <strong>Genap</strong>. Idealnya proses semester baru dulu sampai <strong>Genap</strong>. Tombol ini tetap bisa dipakai untuk data legacy yang belum punya kelas Genap.
            </div>
        @else
            <div class="alert alert-success">
                Data semester akhir sudah mengarah ke <strong>{{ $sourceSemester->nama_semester }}</strong>. Proses tahun ajaran baru siap dijalankan untuk menaikkan kelas X/XI dan meluluskan kelas XII.
            </div>
        @endif

        <div class="alert alert-light border">
            <small class="text-muted">
                Total siswa aktif kelas X pada semester sumber rollover:
                <strong>{{ $gradeXActiveStudentsTotal }}</strong>
                |
                Rombel X kosong:
                <strong>{{ $emptyGradeXClasses->count() }}</strong>
            </small>
        </div>

        <form action="{{ url('admin/kenaikan/proses-tahun-baru') }}"
              method="POST"
              class="mb-3"
              onsubmit="return confirm('Proses tahun ajaran baru akan meluluskan kelas XII dan menaikkan kelas X/XI. Lanjutkan?')">
            @csrf

            <button type="submit" class="btn btn-success btn-sm" {{ $canProcessTahunBaru ? '' : 'disabled' }}>
                Proses Tahun Ajaran Baru
            </button>
        </form>

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">Kelas</th>
                    <th class="text-center">Wali Kelas</th>
                    <th class="text-center">Tahun Ajaran</th>
                    <th class="text-center">Semester</th>
                    <th class="text-center">Jumlah Siswa</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($kelas as $k)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $k->kelas->nama_kelas }}
                    </td>

                    <td>
                        {{ $k->guru->nama_guru }}
                    </td>

                    <td>
                        {{ $k->tahunAjaran->tahun }}
                    </td>

                    <td>
                        {{ $k->semester->nama_semester }}
                    </td>

                    <td class="text-center">
                        {{ $k->jumlah_siswa }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/kenaikan/kelas/'.$k->id_kelas_aktif) }}"
                           class="btn btn-info btn-sm">

                            Detail

                        </a>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
