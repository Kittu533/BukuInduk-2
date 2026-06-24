@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/kelasaktif-form.css') }}">
@endsection

@section('content')

<h3 class="page-title">Tambah Kelas Aktif</h3>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/kelasaktif') }}"
              method="POST">

            @csrf

            {{-- ID KELAS AKTIF --}}
            <div class="mb-3">

                <label class="form-label">
                    ID Kelas Aktif
                </label>

                <input type="text"
                       name="id_kelas_aktif"
                       class="form-control"
                       value="{{ $kode }}"
                       readonly>

            </div>

            {{-- TINGKAT / JURUSAN --}}
            <div class="mb-3">

                <label class="form-label">
                    Tingkat / Jurusan
                </label>

                <select name="prefix_kelas"
                        class="form-control"
                        required>

                    <option value="">
                        -- Pilih Tingkat --
                    </option>

                    <option value="X E"
                        {{ old('prefix_kelas') == 'X E' ? 'selected' : '' }}>
                        X E
                    </option>

                    <option value="XI F"
                        {{ old('prefix_kelas') == 'XI F' ? 'selected' : '' }}>
                        XI F
                    </option>

                    <option value="XII F"
                        {{ old('prefix_kelas') == 'XII F' ? 'selected' : '' }}>
                        XII F
                    </option>

                </select>

            </div>

            {{-- TAHUN AJARAN --}}
            <div class="mb-3">

                <label class="form-label">
                    Tahun Ajaran Aktif
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $tahun->tahun }}"
                       readonly>

            </div>

            {{-- SEMESTER --}}
            <div class="mb-3">

                <label class="form-label">
                    Semester Aktif
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $semester->nama_semester }}"
                       readonly>

            </div>

            {{-- WALI KELAS --}}
            <div class="mb-3">

                <label class="form-label">
                    Wali Kelas
                </label>

                <select name="id_guru"
                        class="form-control"
                        required>

                    <option value="">
                        -- Pilih Wali Kelas --
                    </option>

                    @foreach($guru as $g)

                        <option value="{{ $g->id_guru }}"
                            {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>

                            {{ $g->nama_guru }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- BUTTON --}}
            <button type="submit"
                    class="btn btn-primary">

                Simpan

            </button>

            <a href="{{ url('admin/kelasaktif') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection