@extends('layouts.sidebar-guru')

@section('content')

<h4 class="page-title">Profil Guru</h4>

<div class="card">
<div class="card-body">

<table class="table">

<tr>
    <th width="200">Nama</th>
    <td>{{ $guru->nama_guru }}</td>
</tr>

<tr>
    <th>NIP</th>
    <td>{{ $guru->nip ?? '-' }}</td>
</tr>

<tr>
    <th>Username</th>
    <td>{{ $guru->username }}</td>
</tr>

<tr>
    <th>Email</th>
    <td>{{ $guru->email ?? '-' }}</td>
</tr>

<tr>
    <th>Jenis Kelamin</th>
    <td>{{ $guru->jenis_kelamin ?? '-' }}</td>
</tr>

<tr>
    <th>Jabatan</th>
    <td>{{ $guru->jabatan ?? '-' }}</td>
</tr>

<tr>
    <th>Tugas Mengajar</th>
    <td>{{ $guru->tugas_mengajar ?? '-' }}</td>
</tr>

</table>

<div class="mt-3 d-flex gap-2">

    <a href="{{ url('guru') }}" class="btn btn-secondary">
        ← Kembali
    </a>

    <a href="{{ url('guru/profil/password') }}" class="btn btn-warning">
        🔒 Ubah Password
    </a>

</div>

</div>
</div>

@endsection