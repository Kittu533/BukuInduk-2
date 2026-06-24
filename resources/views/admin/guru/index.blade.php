@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
@endsection

@section('content')

<h3 class="page-title">DATA GURU</h3>

@if(session('success'))

<script>
    alert("{{ session('success') }}");
</script>

@endif

<div class="card table-card">

    <div class="card-body">

        <!-- BUTTON TAMBAH + SEARCH -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <a href="{{ url('admin/guru/create') }}"
            class="btn btn-success btn-sm">
                + Tambah
            </a>

            <form method="GET"
                action="{{ url('admin/guru') }}">

                <div class="d-flex">

                    <input type="text"
                        name="search"
                        class="form-control me-2"
                        style="width:300px"
                        placeholder="Cari Nama Guru, NIP, atau Tugas Mengajar..."
                        value="{{ request('search') }}">

                    <button type="submit"
                            class="btn btn-primary me-2">
                        Cari
                    </button>

                    <a href="{{ url('admin/guru') }}"
                    class="btn btn-secondary">
                        Reset
                    </a>

                </div>

            </form>

        </div>

        <!-- TABEL -->

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th width="60" class="text-center">
                        No
                    </th>

                    <th width="100" class="text-center">
                        ID Guru
                    </th>

                    <th width="250" class="text-center">
                        NIP
                    </th>

                    <th class="text-center">
                        Nama Guru
                    </th>

                    <th class="text-center">
                        Tugas Mengajar
                    </th>

                    <th width="220" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($guru as $g)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td class="text-center">
                        {{ $g->id_guru }}
                    </td>

                    <td>
                        {{ $g->nip }}
                    </td>

                    <td>
                        {{ $g->nama_guru }}
                    </td>

                    <td>
                        {{ $g->tugas_mengajar }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/guru/'.$g->id_guru) }}"
                           class="btn btn-info btn-sm">

                            Detail

                        </a>

                        <a href="{{ url('admin/guru/'.$g->id_guru.'/edit') }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ url('admin/guru/'.$g->id_guru) }}"
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