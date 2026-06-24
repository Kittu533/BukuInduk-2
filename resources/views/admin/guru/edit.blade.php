@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru.css') }}">
@endsection


@section('content')

<h3 class="page-title">EDIT DATA GURU</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/guru/'.$data->id_guru) }}"
              method="POST">

            @csrf
            @method('PUT')


            <!-- ID GURU -->
            <div class="mb-3">

                <label class="form-label">
                    ID Guru
                </label>

                <input type="text"
                       name="id_guru"
                       class="form-control"
                       value="{{ $data->id_guru }}"
                       readonly>

            </div>


            <!-- NAMA GURU -->
            <div class="mb-3">

                <label class="form-label">
                    Nama Guru
                </label>

                <input type="text"
                       name="nama_guru"
                       class="form-control"
                       value="{{ $data->nama_guru }}">

            </div>


            <!-- NIP -->
            <div class="mb-3">

                <label class="form-label">
                    NIP
                </label>

                <input type="text"
                       name="nip"
                       class="form-control"
                       value="{{ $data->nip }}">

            </div>


            <!-- JENIS KELAMIN -->
            <div class="mb-3">

                <label class="form-label">
                    Jenis Kelamin
                </label>

                <select name="jenis_kelamin"
                        class="form-control">

                    <option value="Laki-laki"

                        {{ $data->jenis_kelamin == 'Laki-laki'
                            ? 'selected'
                            : '' }}>

                        Laki-laki

                    </option>


                    <option value="Perempuan"

                        {{ $data->jenis_kelamin == 'Perempuan'
                            ? 'selected'
                            : '' }}>

                        Perempuan

                    </option>

                </select>

            </div>


            <!-- JABATAN -->
            <div class="mb-3">

                <label class="form-label">
                    Jabatan
                </label>

                <input type="text"
                       name="jabatan"
                       class="form-control"
                       value="{{ $data->jabatan }}">

            </div>


            <!-- TUGAS MENGAJAR -->
            <div class="mb-3">

                <label class="form-label">
                    Tugas Mengajar
                </label>

                <input type="text"
                       name="tugas_mengajar"
                       class="form-control"
                       value="{{ $data->tugas_mengajar }}">

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-success btn-sm">

                Update

            </button>


            <a href="{{ url('admin/guru') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection