@extends('layouts.sidebar-guru')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru/absensi.css') }}">
@endsection

@section('content')

<h4 class="page-title">Detail Kehadiran Siswa</h4>

<!-- ================= INFO SISWA ================= -->
<div class="card mb-4">
<div class="card-body p-0">

<table class="table table-bordered mb-0 info-table">

<tr>
    <th width="180">Nama</th>
    <td>{{ $siswa->nama_lengkap }}</td>

    <th>Tahun Ajaran</th>
    <td>{{ $detail->tahun }}</td>
</tr>

<tr>
    <th>NIS</th>
    <td>{{ $siswa->nis ?? '-' }}</td>

    <th>Semester</th>
    <td>{{ $detail->nama_semester }}</td>
</tr>

<tr>
    <th>Kelas</th>
    <td>{{ $detail->nama_kelas }}</td>

    <th>Mata Pelajaran</th>
    <td>{{ $detail->nama_mapel }}</td>
</tr>

</table>

</div>
</div>

<!-- ================= TABLE ABSENSI ================= -->
<div class="card table-card">
<div class="card-body">

<table class="table table-bordered table-striped table-hover">

<thead>
<tr>
    <th width="60">No</th>
    <th>Tanggal</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

@forelse($kehadiran as $k)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $k->tanggal }}</td>
    <td>

        @if($k->status == 'Hadir')
            <span class="badge-hadir">Hadir</span>
        @elseif($k->status == 'Izin')
            <span class="badge-izin">Izin</span>
        @elseif($k->status == 'Sakit')
            <span class="badge-sakit">Sakit</span>
        @else
            <span class="badge-alpha">Alpha</span>
        @endif

    </td>
</tr>

@empty
<tr>
    <td colspan="3" class="text-center text-muted">
        Belum ada data kehadiran
    </td>
</tr>
@endforelse

</tbody>

</table>

<a href="{{ url('guru/absensi/'.$id) }}"
   class="btn btn-secondary mt-3">
   Kembali
</a>

</div>
</div>

@endsection