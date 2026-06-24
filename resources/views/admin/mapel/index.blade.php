@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mapel.css') }}">
@endsection

@section('content')

<h3 class="page-title">DATA MATA PELAJARAN</h3>

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

<div class="card table-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <a href="{{ url('admin/mapel/create') }}"
            class="btn btn-success btn-sm">

                + Tambah

            </a>

            <form method="GET"
                action="{{ url('admin/mapel') }}">

                <div class="d-flex">

                    <select name="semester"
                            class="form-control me-2"
                            style="width:160px">

                        <option value="">
                            Semua Semester
                        </option>

                        <option value="Ganjil"
                            {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>
                            Ganjil
                        </option>

                        <option value="Genap"
                            {{ request('semester') == 'Genap' ? 'selected' : '' }}>
                            Genap
                        </option>

                    </select>

                    <input type="text"
                        name="search"
                        class="form-control me-2"
                        style="width:250px"
                        placeholder="Cari Mapel..."
                        value="{{ request('search') }}">

                    <button type="submit"
                            class="btn btn-primary me-2">

                        Filter

                    </button>

                    <a href="{{ url('admin/mapel') }}"
                    class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </form>

        </div>


        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th width="60" class="text-center">
                        No
                    </th>

                    <th width="120" class="text-center">
                        ID Mapel
                    </th>

                    <th width="140" class="text-center">
                        Semester
                    </th>

                    <th class="text-center">
                        Nama Mapel
                    </th>

                    <th width="200" class="text-center">
                        Kategori
                    </th>

                    <th width="220" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($mapel as $m)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $m->id_mapel }}
                    </td>

                    <td class="text-center">
                        {{ $m->label_semester }}
                    </td>

                    <td>
                        {{ $m->nama_mapel }}
                    </td>

                    <td class="text-center">
                        {{ $m->kategori_mapel }}
                    </td>


                    <td class="text-center">

                        <a href="{{ url('admin/mapel/'.$m->id_mapel.'/edit') }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>


                        <form action="{{ url('admin/mapel/'.$m->id_mapel) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">

                                Hapus

                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
