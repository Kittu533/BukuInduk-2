@extends('layouts.sidebar-guru')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru/absensi.css') }}">
@endsection

@section('content')

<h4 class="page-title">Input Absensi</h4>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form action="{{ url('guru/absensi/simpan') }}" method="POST">
@csrf

<input type="hidden" name="id_siswa" value="{{ $id_siswa }}">
<input type="hidden" name="id_jadwal" value="{{ $id }}">

<div class="card">
<div class="card-body">

<!-- ================= INFO ================= -->
<div class="mb-3">
    <label>Nama Siswa</label>
    <input type="text"
           class="form-control"
           value="{{ $siswa->nama_lengkap }}"
           readonly>
</div>

<!-- ================= TANGGAL ================= -->
<div class="mb-3">
    <label>Tanggal</label>
    <input type="date"
           name="tanggal"
           class="form-control"
           value="{{ $tanggal }}"
           required>
</div>

<!-- ================= STATUS ================= -->
<div class="mb-3">
    <label>Status Kehadiran</label>

    <select name="status" class="form-control" required>
        <option value="">-- Pilih Status --</option>

        <option value="izin"
            {{ ($absen->status ?? '') == 'izin' ? 'selected' : '' }}>
            Izin
        </option>

        <option value="sakit"
            {{ ($absen->status ?? '') == 'sakit' ? 'selected' : '' }}>
            Sakit
        </option>

        <option value="alpa"
            {{ ($absen->status ?? '') == 'alpa' ? 'selected' : '' }}>
            Alpa
        </option>
    </select>
</div>

<!-- ================= BUTTON ================= -->
<div class="form-action">
    <button class="btn btn-primary">Simpan</button>

    <a href="{{ url('guru/absensi/'.$id) }}"
       class="btn btn-secondary">
       Kembali
    </a>
</div>

</div>
</div>

</form>

@endsection