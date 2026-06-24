@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">PROFIL SEKOLAH</h3>

<div class="card">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ url('admin/profil-sekolah/edit') }}" class="btn btn-warning btn-sm">Edit Profil</a>
            @if($profil)
                <a href="{{ url('admin/profil-sekolah/download-cover') }}" class="btn btn-primary btn-sm">Download Cover</a>
            @endif
        </div>

        @if($profil)
            <table class="table table-bordered">
                <tr><th width="200">Nama Sekolah</th><td>{{ $profil->nama_sekolah }}</td></tr>
                <tr><th>Alamat</th><td>{{ $profil->alamat }}</td></tr>
                <tr><th>Telepon</th><td>{{ $profil->telepon }}</td></tr>
                <tr><th>Email</th><td>{{ $profil->email }}</td></tr>
                <tr><th>Website</th><td>{{ $profil->website }}</td></tr>
                <tr><th>Kepala Sekolah</th><td>{{ $profil->kepala_sekolah }}</td></tr>
                <tr><th>NIP Kepala Sekolah</th><td>{{ $profil->nip_kepala_sekolah }}</td></tr>
                <tr><th>Akreditasi</th><td>{{ $profil->akreditasi }}</td></tr>
                <tr><th>NPSN</th><td>{{ $profil->npsn }}</td></tr>
                <tr><th>Logo</th><td>
                    @if($profil->logo)
                        <img src="{{ asset('storage/'.$profil->logo) }}" alt="Logo" width="100">
                    @else
                        <span class="text-muted">Belum diupload</span>
                    @endif
                </td></tr>
            </table>
        @else
            <p class="text-muted">Profil sekolah belum diisi. Silakan klik "Edit Profil" untuk mengisi data.</p>
        @endif

    </div>
</div>

@endsection
