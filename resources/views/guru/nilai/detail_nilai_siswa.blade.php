@extends('layouts.sidebar-guru')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru/nilai.css') }}">
@endsection

@section('content')

<h4 class="page-title mb-3">Detail Nilai Siswa</h4>

<!-- ================= INFO SISWA ================= -->
<div class="card mb-4">
<div class="card-body p-0">

<table class="table table-bordered mb-0 info-table">

<tr>
    <th width="180">Nama</th>
    <td>{{ $siswa->nama_lengkap }}</td>

    <th width="180">Tahun Ajaran</th>
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
    <td>{{ $detail->nama_mapel }} ({{ $detail->kode_mapel_semester }})</td>
</tr>

</table>

</div>
</div>

<!-- ================= TABLE NILAI ================= -->
<div class="card">
<div class="card-body">

<table class="table table-bordered table-striped table-hover table-nilai text-center align-middle">

<thead>
<tr>
    <th width="60">No</th>
    <th>Mata Pelajaran</th>
    <th>T1</th>
    <th>T2</th>
    <th>T3</th>
    <th>T4</th>
    <th>T5</th>
    <th>UTS</th>
    <th>UAS</th>
    <th>Nilai Akhir</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

<tr>
    <td>1</td>

    <td class="text-start fw-semibold">
        {{ $detail->nama_mapel }}
    </td>

    <!-- tugas -->
    <td class="{{ ($nilai->tugas1 ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->tugas1 ?? '-' }}
    </td>

    <td class="{{ ($nilai->tugas2 ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->tugas2 ?? '-' }}
    </td>

    <td class="{{ ($nilai->tugas3 ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->tugas3 ?? '-' }}
    </td>

    <td class="{{ ($nilai->tugas4 ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->tugas4 ?? '-' }}
    </td>

    <td class="{{ ($nilai->tugas5 ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->tugas5 ?? '-' }}
    </td>

    <!-- uts -->
    <td class="{{ ($nilai->uts ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->uts ?? '-' }}
    </td>

    <!-- uas -->
    <td class="{{ ($nilai->uas ?? 0) < 75 ? 'text-danger' : '' }}">
        {{ $nilai->uas ?? '-' }}
    </td>

    <!-- nilai akhir -->
    <td class="fw-bold 
        {{ is_null($nilai?->nilai_akhir) ? '' : (($nilai->nilai_akhir ?? 0) < 75 ? 'text-danger' : 'text-success') }}">
        {{ $nilai->nilai_akhir ?? '-' }}
    </td>

    <!-- STATUS -->
    <td>
        @if(is_null($nilai?->nilai_akhir))
            <span class="badge bg-secondary">Belum Lengkap</span>
        @elseif(($nilai->nilai_akhir ?? 0) < 75)
            <span class="badge bg-danger">Tidak Tuntas</span>
        @else
            <span class="badge bg-success">Tuntas</span>
        @endif
    </td>

</tr>

</tbody>

</table>

<!-- BUTTON -->
<a href="{{ url('guru/nilai/'.$id) }}"
   class="btn btn-secondary mt-3">
   Kembali
</a>

</div>
</div>

@endsection
