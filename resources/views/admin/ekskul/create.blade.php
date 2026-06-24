@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/ekskul.css') }}">
@endsection


@section('content')

<h3 class="page-title">TAMBAH DATA EKSTRAKURIKULER</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/ekskul') }}"
              method="POST">

            @csrf


            <!-- ID EKSKUL -->
            <div class="mb-3">

                <label class="form-label">
                    ID Ekskul
                </label>

                <input type="text"
                    name="id_ekskul"
                    class="form-control"
                    value="{{ $kode }}"
                    readonly>

            </div>


            <!-- NAMA EKSKUL -->
            <div class="mb-3">

                <label class="form-label">
                    Nama Ekskul
                </label>

                <input type="text"
                       name="nama_ekskul"
                       class="form-control"
                       placeholder="Masukkan nama ekskul"
                       required>

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-primary btn-sm">

                Simpan

            </button>


            <a href="{{ url('admin/ekskul') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection