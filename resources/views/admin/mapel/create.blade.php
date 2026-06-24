@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mapel.css') }}">
@endsection

@section('content')

<h3 class="page-title">TAMBAH MATA PELAJARAN</h3>

<div class="card form-card">

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>
        @endif

        <form action="{{ url('admin/mapel') }}"
              method="POST">

            @csrf

            {{-- KATEGORI MAPEL --}}
            <div class="mb-3">

                <label class="form-label">
                    Kategori Mata Pelajaran
                </label>

                <select name="kategori_mapel"
                        class="form-control"
                        required>

                    <option value="">
                        -- Pilih Kategori --
                    </option>

                    <option value="Wajib"
                        {{ old('kategori_mapel') == 'Wajib' ? 'selected' : '' }}>

                        Wajib

                    </option>

                    <option value="Pilihan"
                        {{ old('kategori_mapel') == 'Pilihan' ? 'selected' : '' }}>

                        Pilihan

                    </option>

                </select>

            </div>

            {{-- TINGKAT --}}
            <div class="mb-3">

                <label class="form-label">
                    Tingkat Kelas
                </label>

                <select name="tingkat"
                        class="form-control"
                        required>

                    <option value="">
                        -- Pilih Tingkat --
                    </option>

                    <option value="X"
                        {{ old('tingkat') == 'X' ? 'selected' : '' }}>

                        X

                    </option>

                    <option value="XI"
                        {{ old('tingkat') == 'XI' ? 'selected' : '' }}>

                        XI

                    </option>

                    <option value="XII"
                        {{ old('tingkat') == 'XII' ? 'selected' : '' }}>

                        XII

                    </option>

                </select>

            </div>

            {{-- SEMESTER --}}
            <div class="mb-3">

                <label class="form-label">
                    Semester
                </label>

                <select name="semester_mapel"
                        class="form-control"
                        required>

                    <option value="">
                        -- Pilih Semester --
                    </option>

                    <option value="Ganjil"
                        {{ old('semester_mapel') == 'Ganjil' ? 'selected' : '' }}>

                        Ganjil

                    </option>

                    <option value="Genap"
                        {{ old('semester_mapel') == 'Genap' ? 'selected' : '' }}>

                        Genap

                    </option>

                </select>

            </div>

            {{-- KODE DASAR --}}
            <div class="mb-3">

                <label class="form-label">
                    Kode Dasar Mapel
                </label>

                <input type="number"
                       name="kode_dasar"
                       class="form-control"
                       placeholder="Contoh : 001"
                       value="{{ old('kode_dasar') }}"
                       required>

                <small class="text-muted">

                    Contoh:
                    001 = Biologi,
                    002 = Fisika,
                    003 = Kimia

                </small>

            </div>

            {{-- NAMA MAPEL --}}
            <div class="mb-3">

                <label class="form-label">
                    Nama Mata Pelajaran
                </label>

                <input type="text"
                       name="nama_mapel"
                       class="form-control"
                       placeholder="Masukkan Nama Mata Pelajaran"
                       value="{{ old('nama_mapel') }}"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-primary btn-sm">

                Simpan

            </button>

            <a href="{{ url('admin/mapel') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection