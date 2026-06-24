@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/semester.css') }}">
@endsection

@section('content')

<h3 class="page-title">Edit Semester</h3>

<div class="card table-card">

    <div class="card-body">

        <form action="{{ url('admin/semester/'.$semester->id_semester) }}"
              method="POST">

            @csrf
            @method('PUT')


            <!-- ID Semester -->
            <div class="mb-3">

                <label>ID Semester</label>

                <input type="text"
                       class="form-control"
                       value="{{ $semester->id_semester }}"
                       readonly>

            </div>


            <!-- Tahun Ajaran -->
            <div class="mb-3">

                <label>Tahun Ajaran</label>

                <input type="text"
                       class="form-control"
                       value="{{ $semester->tahunAjaran->tahun ?? '-' }}"
                       readonly>

            </div>


            <!-- Nama Semester -->
            <div class="mb-3">

                <label>Semester</label>

                <select name="nama_semester"
                        class="form-control">

                    <option value="Ganjil"
                    {{ $semester->nama_semester == 'Ganjil' ? 'selected' : '' }}>

                        Ganjil

                    </option>

                    <option value="Genap"
                    {{ $semester->nama_semester == 'Genap' ? 'selected' : '' }}>

                        Genap

                    </option>

                </select>

            </div>


            <button type="submit"
                    class="btn btn-primary">

                Update

            </button>


            <a href="{{ url('admin/semester') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection