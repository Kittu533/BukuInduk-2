@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/kelola-akun.css') }}">
@endsection

@section('content')

<h3 class="page-title">
    DATA KELOLA AKUN
</h3>

@if(session('success'))

<script>
    alert("{{ session('success') }}");
</script>

@endif


<div class="card table-card">

    <div class="card-body">


        <!-- TOP ACTION -->
        <div class="top-action">

            <a href="{{ url('admin/kelola-akun/create') }}"
               class="btn btn-success btn-sm">

                + Tambah

            </a>


            <!-- SEARCH & FILTER -->
            <form action="{{ url('admin/kelola-akun') }}"
                  method="GET"
                  class="search-form">


                <select name="kelas"
                <select name="kelas"
                        class="form-control search-input">

                    <option value="">
                        Semua Kelas
                    </option>

                    @foreach($kelas as $k)

                        <option value="{{ $k->id_kelas }}"
                            {{ request('kelas') == $k->id_kelas ? 'selected' : '' }}>

                            {{ $k->nama_kelas }}

                        </option>

                    @endforeach

                </select>


                <!-- SEARCH -->
                <input type="text"
                       name="search"
                       class="form-control search-input"
                       style="width:180px;"
                       placeholder="Cari akun..."
                       value="{{ request('search') }}">


                <!-- BUTTON -->
                <button class="btn btn-success btn-sm">

                    Cari

                </button>

            </form>

        </div>


        <!-- TABLE -->
        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th width="60" class="text-center">
                        No
                    </th>

                    <th class="text-center">
                        Nama
                    </th>

                    <th class="text-center">
                        Username
                    </th>

                    <th class="text-center">
                        Email
                    </th>

                    <th class="text-center">
                        Role
                    </th>

                    <th width="180" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($users as $item)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>


                    <td>
                        {{ $item->name }}
                    </td>


                    <td>
                        {{ $item->username }}
                    </td>


                    <td>
                        {{ $item->email }}
                    </td>


                    <td class="text-center">
                        {{ ucfirst($item->role) }}
                    </td>


                    <td class="text-center">

                        <a href="{{ url('admin/kelola-akun/'.$item->id_users.'/edit') }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ url('admin/kelola-akun/'.$item->id_users) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin hapus data?')">

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

{{ $users->links() }}

@endsection