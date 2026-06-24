@extends('layouts.sidebar-walas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">HASIL BELAJAR SISWA</h3>
</div>

@if($riwayat->count() > 1)
<form method="GET" class="mb-3 d-flex justify-content-end align-items-center gap-2">
    <label class="small text-muted">Riwayat</label>
    <select name="kelas_aktif" class="form-select form-select-sm" style="max-width: 380px;" onchange="this.form.submit()">
        @foreach($riwayat as $item)
            <option value="{{ $item->id_kelas_aktif }}" {{ request('kelas_aktif', $kelas_walas?->id_kelas_aktif) == $item->id_kelas_aktif ? 'selected' : '' }}>
                {{ $item->kelasAktif->kelas->nama_kelas ?? '-' }} - {{ $item->kelasAktif->semester->nama_semester ?? '-' }} / {{ $item->kelasAktif->tahunAjaran->tahun ?? '-' }}
            </option>
        @endforeach
    </select>
</form>
@endif

<!-- ================= DATA SISWA ================= -->

<table class="table table-bordered">

    <tr>

        <td width="150" class="judul">
            Nama
        </td>

        <td>
            {{ $siswa->nama_lengkap }}
        </td>

        <td width="150" class="right">
            <b>Tahun Ajaran</b>
        </td>

        <td>
            {{ $kelas_walas->tahun ?? '-' }}
        </td>

    </tr>

    <tr>

        <td class="judul">
            NIS
        </td>

        <td>
            {{ $siswa->nis }}
        </td>

        <td class="right">
            <b>Semester</b>
        </td>

        <td>
            {{ $kelas_walas->nama_semester ?? '-' }}
        </td>

    </tr>

</table>

<!-- ================= NILAI (ASLI JANGAN DIUBAH) ================= -->

<table class="table table-bordered">

    <tr>
        <th class="text-center">No</th>
        <th class="text-center">Kode</th>
        <th class="text-center">Mata Pelajaran</th>
        <th class="text-center">T1</th>
        <th class="text-center">T2</th>
        <th class="text-center">T3</th>
        <th class="text-center">T4</th>
        <th class="text-center">T5</th>
        <th class="text-center">UTS</th>
        <th class="text-center">UAS</th>
    </tr>

    <tr>
        <td colspan="11" class="text-center"><b>Kelompok Umum</b></td>
    </tr>

    @foreach($nilai_wajib as $n)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $n->kode_mapel_semester }}</td>
        <td>{{ $n->nama_mapel }}</td>
        <td>{{ $n->tugas1 }}</td>
        <td>{{ $n->tugas2 }}</td>
        <td>{{ $n->tugas3 }}</td>
        <td>{{ $n->tugas4 }}</td>
        <td>{{ $n->tugas5 }}</td>
        <td>{{ $n->uts }}</td>
        <td>{{ $n->uas }}</td>
    </tr>
    @endforeach

    <tr>
        <td colspan="11" class="text-center"><b>Kelompok Pilihan</b></td>
    </tr>

    @foreach($nilai_pilihan as $n)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $n->kode_mapel_semester }}</td>
        <td>{{ $n->nama_mapel }}</td>
        <td>{{ $n->tugas1 }}</td>
        <td>{{ $n->tugas2 }}</td>
        <td>{{ $n->tugas3 }}</td>
        <td>{{ $n->tugas4 }}</td>
        <td>{{ $n->tugas5 }}</td>
        <td>{{ $n->uts }}</td>
        <td>{{ $n->uas }}</td>
    </tr>
    @endforeach

</table>

<!-- ================= EKSKUL ================= -->

<br>

<h5><b>Ekstrakurikuler</b></h5>

<table class="table table-bordered">

    <tr>
        <th width="60" class="text-center">No</th>
        <th class="text-center">Nama Ekskul</th>
        <th width="120" class="text-center">Nilai</th>
    </tr>

    @forelse($ekskul as $e)

    <tr>

        <td class="text-center">
            {{ $loop->iteration }}
        </td>

        <td>
            {{ $e->ekstrakurikuler->nama_ekskul ?? '-' }}
        </td>

        <td class="text-center">

            @if($e->nilai == 'A')

                <span class="badge bg-success">
                    A
                </span>

            @elseif($e->nilai == 'B')

                <span class="badge bg-primary">
                    B
                </span>

            @elseif($e->nilai == 'C')

                <span class="badge bg-warning text-dark">
                    C
                </span>

            @else

                <span class="badge bg-secondary">
                    {{ $e->nilai }}
                </span>

            @endif

        </td>

    </tr>

    @empty

    <tr>

        <td colspan="3" class="text-center text-muted">
            Tidak ada data ekskul
        </td>

    </tr>

    @endforelse

</table>

<!-- ================= ABSENSI ================= -->

<br>

<h5><b>Ketidakhadiran</b></h5>

<table class="table table-bordered">

    <tr class="text-center">
        <th width="200">Keterangan</th>
        <th width="120">Jumlah Hari</th>
    </tr>

    <tr>
        <td>Sakit</td>
        <td class="text-center">{{ $absen->sakit ?? 0 }}</td>
    </tr>

    <tr>
        <td>Izin</td>
        <td class="text-center">{{ $absen->izin ?? 0 }}</td>
    </tr>

    <tr>
        <td>Tanpa Keterangan</td>
        <td class="text-center">{{ $absen->alpa ?? 0 }}</td>
    </tr>

</table>

<div class="mt-3">

    <a href="{{ url('wali/rekap') }}"
       class="btn btn-secondary btn-sm">

        Kembali

    </a>

</div>

@endsection
