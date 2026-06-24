@extends('layouts.sidebar-guru')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru/absensi.css') }}">
@endsection

@section('content')

<h4 class="fw-bold mb-3">Daftar Siswa</h4>

<!-- ================= INFO KELAS ================= -->
<div class="card mb-4">
<div class="card-body p-0">

<table class="table table-bordered mb-0 info-table">

<tr>
    <th width="180">Kelas</th>
    <td>{{ $detail->nama_kelas }}</td>

    <th width="180">Mata Pelajaran</th>
    <td>{{ $detail->nama_mapel }}</td>
</tr>

<tr>
    <th>Tahun Ajaran</th>
    <td>{{ $detail->tahun ?? '-' }}</td>

    <th>Semester</th>
    <td>{{ $detail->nama_semester ?? '-' }}</td>
</tr>

</table>

</div>
</div>

{{-- ALERT --}}
@if(!$canEdit)

<div class="alert alert-warning">

    @if(str_starts_with(strtoupper($detail->nama_kelas), 'XII'))

        Kelas XII sudah lulus. Data absensi masih dapat dilihat, tetapi tidak dapat ditambahkan atau diubah.

    @else

        Semester ini sudah ditutup. Data absensi masih dapat dilihat, tetapi tidak dapat input.

    @endif

</div>

@endif

<!-- ================= TABLE ================= -->
<div class="card table-card">
<div class="card-body">

<table class="table table-bordered table-striped table-hover">

<thead>
<tr>
    <th width="60" class="text-center">No</th>
    <th class="text-center">Nama Siswa</th>
    <th width="180" class="text-center">Aksi</th>
</tr>
</thead>

<tbody>

@forelse($siswa as $s)

<tr>

    <td class="text-center">
        {{ $loop->iteration }}
    </td>

    <td>
        {{ $s->nama_lengkap }}
    </td>

    <td class="text-center">

        <a href="{{ url('guru/absensi/'.$id.'/'.$s->id_siswa.'/detail') }}"
           class="btn btn-info btn-sm btn-aksi">
            Detail
        </a>

        @if($canEdit)

            <a href="{{ url('guru/absensi/'.$id.'/'.$s->id_siswa.'/input') }}"
               class="btn btn-success btn-sm btn-aksi">
                Input
            </a>

        @else

            <button class="btn btn-secondary btn-sm btn-aksi" disabled>

                @if(str_starts_with(strtoupper($detail->nama_kelas), 'XII'))
                    Lulus
                @else
                    Input Ditutup
                @endif

            </button>

        @endif

    </td>

</tr>

@empty

<tr>
    <td colspan="3" class="text-center text-muted">
        Tidak ada data siswa
    </td>
</tr>

@endforelse

</tbody>

</table>

<a href="{{ url('guru/absensi') }}"
   class="btn btn-secondary mt-3">
    Kembali
</a>

</div>
</div>

@endsection