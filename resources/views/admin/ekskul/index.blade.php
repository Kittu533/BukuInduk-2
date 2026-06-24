@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">DATA EKSTRAKURIKULER</h3>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card table-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-end mb-3">

            <a href="{{ url('admin/ekskul/create') }}"
               class="btn btn-success btn-sm">

                + Tambah

            </a>

            <form method="GET"
                  action="{{ url('admin/ekskul') }}">

                <div class="d-flex align-items-center">

                    <input type="text"
                           name="search"
                           class="form-control me-2"
                           style="width:300px;"
                           placeholder="Cari ID atau Nama Ekskul..."
                           value="{{ request('search') }}">

                    <button type="submit"
                            class="btn btn-primary me-2">

                        Filter

                    </button>

                    <a href="{{ url('admin/ekskul') }}"
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

                    <th width="150" class="text-center">
                        ID Ekskul
                    </th>

                    <th class="text-center">
                        Nama Ekskul
                    </th>

                    <th width="180" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($ekskul as $e)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td class="text-center">
                        {{ $e->id_ekskul }}
                    </td>

                    <td>
                        {{ $e->nama_ekskul }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/ekskul/'.$e->id_ekskul.'/edit') }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ url('admin/ekskul/'.$e->id_ekskul) }}"
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

                @empty

                <tr>

                    <td colspan="4" class="text-center">
                        Data tidak ditemukan
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection