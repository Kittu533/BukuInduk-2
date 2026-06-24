@extends('layouts.sidebar-guru')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru/nilai.css') }}">
@endsection

@section('content')

<h4 class="page-title mb-3">Input Nilai Siswa</h4>

@if(session('success'))
<script>alert("{{ session('success') }}")</script>
@endif

<!-- INFO -->
<div class="info-box mb-4">

    <span class="badge badge-kelas">
        Kelas: {{ $detail->nama_kelas }}
    </span>

    <span class="badge badge-mapel">
        Mapel: {{ $detail->nama_mapel }} ({{ $detail->kode_mapel_semester }})
    </span>

</div>

<div class="card">
<div class="card-body">

<form action="{{ url('guru/nilai/simpan') }}" method="POST">
@csrf

<input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
<input type="hidden" name="id_jadwal" value="{{ $jadwalId }}">

<!-- NAMA -->
<div class="mb-4">
    <label class="form-label fw-semibold">Nama Siswa</label>
    <input type="text" class="form-control" value="{{ $siswa->nama_lengkap }}" readonly>
</div>

<!-- NILAI -->
<div class="row">

    <div class="col-md-6 mb-3">
        <label>Tugas 1</label>
        <input type="number" name="tugas1" class="form-control"
               value="{{ $nilai->tugas1 ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Tugas 2</label>
        <input type="number" name="tugas2" class="form-control"
               value="{{ $nilai->tugas2 ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Tugas 3</label>
        <input type="number" name="tugas3" class="form-control"
               value="{{ $nilai->tugas3 ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Tugas 4</label>
        <input type="number" name="tugas4" class="form-control"
               value="{{ $nilai->tugas4 ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Tugas 5</label>
        <input type="number" name="tugas5" class="form-control"
               value="{{ $nilai->tugas5 ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>UTS</label>
        <input type="number" name="uts" class="form-control"
               value="{{ $nilai->uts ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>UAS</label>
        <input type="number" name="uas" class="form-control"
               value="{{ $nilai->uas ?? '' }}">
    </div>

</div>

<!-- BUTTON -->
<div class="form-action mt-4">

    <button class="btn btn-primary">
        Simpan
    </button>

    <a href="{{ url('guru/nilai/'.$jadwalId) }}"
       class="btn btn-secondary">
        Kembali
    </a>

</div>

</form>

</div>
</div>

@endsection
