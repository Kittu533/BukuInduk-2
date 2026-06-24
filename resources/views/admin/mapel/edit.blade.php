@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mapel.css') }}">
@endsection


@section('content')

<h3 class="page-title">EDIT DATA MATA PELAJARAN</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/mapel/'.$data->id_mapel) }}"
              method="POST">

            @csrf
            @method('PUT')


            <!-- ID MAPEL -->
            <div class="mb-3">

                <label class="form-label">
                    ID Mapel
                </label>

                <input type="text"
                       name="id_mapel"
                       class="form-control"
                       value="{{ $data->id_mapel }}"
                       readonly>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Semester Mapel
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data->label_semester }}"
                       readonly>

            </div>


            <!-- NAMA MAPEL -->
            <div class="mb-3">

                <label class="form-label">
                    Nama Mata Pelajaran
                </label>

                <input type="text"
                       name="nama_mapel"
                       class="form-control"
                       value="{{ $data->nama_mapel }}"
                       required>

            </div>


            <!-- KATEGORI -->
            <div class="mb-3">

                <label class="form-label">
                    Kategori Mapel
                </label>

                <select name="kategori_mapel"
                        class="form-control">

                    <option value="Wajib"

                        {{ $data->kategori_mapel == 'Wajib'
                            ? 'selected'
                            : '' }}>

                        Wajib

                    </option>


                    <option value="Pilihan"

                        {{ $data->kategori_mapel == 'Pilihan'
                            ? 'selected'
                            : '' }}>

                        Pilihan

                    </option>

                </select>

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-success btn-sm">

                Update

            </button>


            <a href="{{ url('admin/mapel') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection
