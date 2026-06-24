@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/ekskul.css') }}">
@endsection


@section('content')

<h3 class="page-title">EDIT DATA EKSTRAKURIKULER</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/ekskul/'.$data->id_ekskul) }}"
              method="POST">

            @csrf
            @method('PUT')


            <!-- ID EKSKUL -->
            <div class="mb-3">

                <label class="form-label">
                    ID Ekskul
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data->id_ekskul }}"
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
                       value="{{ $data->nama_ekskul }}"
                       required>

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-success btn-sm">

                Update

            </button>


            <a href="{{ url('admin/ekskul') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection