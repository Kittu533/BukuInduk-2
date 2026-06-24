@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/jadwal-mengajar.css') }}">
@endsection


@section('content')

<h3 class="page-title">EDIT JADWAL MENGAJAR</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/jadwal-mengajar/'.$data->id_jadwal) }}"
              method="POST">

            @csrf
            @method('PUT')


            <!-- ID JADWAL -->
            <div class="mb-3">

                <label class="form-label">
                    ID Jadwal
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data->id_jadwal }}"
                       readonly>

            </div>


            <!-- GURU -->
            <div class="mb-3">

                <label class="form-label">
                    Guru
                </label>

                <select name="id_guru"
                        class="form-control"
                        required>

                    @foreach($guru as $g)

                    <option value="{{ $g->id_guru }}"

                        {{ $data->id_guru == $g->id_guru
                            ? 'selected'
                            : '' }}>

                        {{ $g->nama_guru }}

                    </option>

                    @endforeach

                </select>

            </div>


            <!-- MAPEL -->
            <div class="mb-3">

                <label class="form-label">
                    Mata Pelajaran
                </label>

                <select name="id_mapel"
                        class="form-control"
                        required>

                    @foreach($mapel as $m)

                    <option value="{{ $m->id_mapel }}"

                        {{ $data->id_mapel == $m->id_mapel
                            ? 'selected'
                            : '' }}>

                        {{ $m->id_mapel }}
                        -
                        {{ $m->nama_mapel }}
                        -
                        {{ $m->kategori_mapel }}
                        -
                        {{ data_get($m, 'semester_mapel') ?? '-' }}

                    </option>

                    @endforeach

                </select>

            </div>


            <!-- KELAS AKTIF -->
            <div class="mb-3">

                <label class="form-label">
                    Kelas Aktif
                </label>

                <select name="id_kelas_aktif"
                        class="form-control"
                        required>

                    @foreach($kelasaktif as $k)

                    <option value="{{ $k->id_kelas_aktif }}"

                        {{ $data->id_kelas_aktif == $k->id_kelas_aktif
                            ? 'selected'
                            : '' }}>

                        {{ $k->nama_kelas }} - {{ $k->nama_semester }}

                    </option>

                    @endforeach

                </select>

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-success btn-sm">

                Update

            </button>


            <a href="{{ url('admin/jadwal-mengajar') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection
