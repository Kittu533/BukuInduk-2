@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">DATA KELAS</h3>

@if(session('success'))
<script>
    alert("{{ session('success') }}");
</script>
@endif

<div class="card table-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-end mb-3">

            @if(strtolower($semesterAktif->nama_semester) == 'ganjil')
                <a href="{{ url('admin/kelasaktif/create') }}"
                class="btn btn-success btn-sm">
                    + Tambah
                </a>
            @endif

            <form method="GET"
                  action="{{ url('admin/kelasaktif') }}">

                <div class="d-flex">

                    <select name="id_tahun"
                            class="form-control me-2" style="min-width: 120px;">

                        @foreach($tahunAjaran as $ta)

                            <option
                                value="{{ $ta->id_tahun }}"
                                {{ $idTahun == $ta->id_tahun ? 'selected' : '' }}>

                                {{ $ta->tahun }}

                            </option>

                        @endforeach

                    </select>

                    <select name="semester"
                            class="form-control me-2">

                        @foreach($semester as $s)

                            <option
                                value="{{ $s->nama_semester }}"
                                {{ $namaSemester == $s->nama_semester ? 'selected' : '' }}>

                                {{ $s->nama_semester }}

                            </option>

                        @endforeach

                    </select>

                    <button type="submit"
                            class="btn btn-primary me-2">

                        Filter

                    </button>

                    <a href="{{ url('admin/kelasaktif') }}"
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

                    <th class="text-center">
                        Kelas
                    </th>

                    <th class="text-center">
                        Tahun Ajaran
                    </th>

                    <th class="text-center">
                        Semester
                    </th>

                    <th class="text-center">
                        Wali Kelas
                    </th>

                    <th width="220" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($kelasaktif as $k)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $k->kelas->nama_kelas ?? '-' }}
                        </td>

                        <td>
                            {{ $k->tahunAjaran->tahun ?? '-' }}
                        </td>

                        <td>
                            {{ $k->semester->nama_semester ?? '-' }}
                        </td>

                        <td>
                            {{ $k->guru->nama_guru ?? '-' }}
                        </td>

                        <td class="text-center">

                            <a href="{{ url('admin/kelasaktif/'.$k->id_kelas_aktif.'/edit') }}"
                               class="btn btn-warning btn-sm">

                                Edit

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"
                            class="text-center">

                            Data tidak ditemukan

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
