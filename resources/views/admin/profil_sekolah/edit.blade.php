@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">EDIT PROFIL SEKOLAH</h3>

<div class="card">
    <div class="card-body">

        <form action="{{ url('admin/profil-sekolah') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                <input type="text" name="nama_sekolah" class="form-control" value="{{ $profil->nama_sekolah ?? '' }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2">{{ $profil->alamat ?? '' }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="{{ $profil->telepon ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $profil->email ?? '' }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" name="website" class="form-control" value="{{ $profil->website ?? '' }}">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" class="form-control" value="{{ $profil->kepala_sekolah ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIP Kepala Sekolah</label>
                    <input type="text" name="nip_kepala_sekolah" class="form-control" value="{{ $profil->nip_kepala_sekolah ?? '' }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Akreditasi</label>
                    <input type="text" name="akreditasi" class="form-control" value="{{ $profil->akreditasi ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">NPSN</label>
                    <input type="text" name="npsn" class="form-control" value="{{ $profil->npsn ?? '' }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Logo Sekolah</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                @if($profil && $profil->logo)
                    <small class="text-muted">Logo saat ini: {{ $profil->logo }}</small>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ url('admin/profil-sekolah') }}" class="btn btn-secondary">Kembali</a>
        </form>

    </div>
</div>

@endsection
