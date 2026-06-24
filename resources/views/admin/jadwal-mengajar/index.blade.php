@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/jadwal-mengajar.css') }}">
@endsection


@section('content')

<h3 class="page-title">
    DATA JADWAL MENGAJAR
</h3>

@if(session('success'))

<script>
    alert("{{ session('success') }}");
</script>

@endif


<div class="card table-card">

    <div class="card-body">

        <!-- TOP ACTION -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <a href="{{ url('admin/jadwal-mengajar/create') }}"
            class="btn btn-success btn-sm">

                + Tambah

            </a>

            <form method="GET"
                action="{{ url('admin/jadwal-mengajar') }}">

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
                        style="width:280px"
                        placeholder="Cari ID Guru, Nama Guru, ID Mapel, atau Nama Mapel..."
                        value="{{ request('search') }}">

                    <button type="submit"
                            class="btn btn-primary me-2">

                        Filter

                    </button>

                    <a href="{{ url('admin/jadwal-mengajar') }}"
                    class="btn btn-secondary">

                        Reset

                    </a>

                </div>

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
                        Guru
                    </th>

                    <th class="text-center">
                        Mata Pelajaran
                    </th>

                    <th width="140" class="text-center">
                        Semester
                    </th>

                    <th width="150" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($jadwal as $j)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>


                    <td>
                        {{ $j->nama_guru }}
                    </td>


                    <td>
                        {{ $j->id_mapel }} - {{ $j->nama_mapel }}
                    </td>

                    <td class="text-center">
                        {{ data_get($j, 'semester_mapel') ?? '-' }}
                    </td>


                    <td class="text-center">
                    <a href="{{ url('/admin/jadwal-mengajar/'.$j->id_guru.'?mapel='.$j->id_mapel) }}"
                    class="btn btn-info btn-sm">
                        Detail
                    </a>
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
