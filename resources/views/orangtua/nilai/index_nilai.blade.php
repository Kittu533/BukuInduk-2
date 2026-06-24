@extends('layouts.ortu-app')

@push('styles')

<link rel="stylesheet"
      href="{{ asset('css/nilai-ortu.css') }}">

@endpush

@section('content')

<div class="nilai-wrapper">
<h3 class="text-center nilai-title">
    HASIL BELAJAR SEMESTER AKTIF
</h3>

<div class="d-flex justify-content-end mb-3">

    <a href="{{ url('orangtua/download-nilai') }}"
       class="btn btn-success btn-sm me-2">
        <i class="bi bi-file-earmark-pdf"></i>
        Download Nilai
    </a>

    <a href="{{ url('orangtua/rekap') }}"
       class="btn btn-primary btn-sm">
        <i class="bi bi-journal-text"></i>
        Lihat Rekap 6 Semester
    </a>

</div>

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
        <td colspan="10" class="text-center"><b>Kelompok Umum</b></td>
    </tr>

    @foreach($nilai_wajib as $n)
    <tr>
        <td>{{ $loop->iteration }}</td>
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
        <td colspan="10" class="text-center"><b>Kelompok Pilihan</b></td>
    </tr>

    @foreach($nilai_pilihan as $n)
    <tr>
        <td>{{ $loop->iteration }}</td>
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
        <td class="text-center">{{ $loop->iteration }}</td>
        <td>{{ $e->nama_ekskul }}</td>

        <td class="text-center">

            @if($e->nilai == 'A')
                <span class="badge bg-success">A</span>
            @elseif($e->nilai == 'B')
                <span class="badge bg-primary">B</span>
            @else
                <span class="badge bg-danger text-dark">C</span>
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

    <a href="{{ url('orangtua/dashboard') }}"
       class="btn btn-secondary btn-sm">
        Kembali
    </a>

</div>
</div>

@endsection