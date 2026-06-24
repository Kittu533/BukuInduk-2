@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/jadwal-mengajar.css') }}">
@endsection


@section('content')

<h3 class="page-title">TAMBAH JADWAL MENGAJAR</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/jadwal-mengajar') }}"
              method="POST">

            @csrf


            <!-- ID JADWAL -->
            <div class="mb-3">

                <label class="form-label">
                    ID Jadwal
                </label>

                <input type="text"
                       name="id_jadwal"
                       class="form-control"
                       value="{{ $kode }}"
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

                    <option value="">
                        -- Pilih Guru --
                    </option>

                    @foreach($guru as $g)

                    <option value="{{ $g->id_guru }}">

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

                    <option value="">
                        -- Pilih Mata Pelajaran --
                    </option>

                    @foreach($mapel as $m)

                    <option value="{{ $m->id_mapel }}">

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

                    <option value="">
                        -- Pilih Kelas --
                    </option>

                    @foreach($kelasaktif as $k)

                    <option value="{{ $k->id_kelas_aktif }}">

                        {{ $k->nama_kelas }} - {{ $k->nama_semester }}

                    </option>

                    @endforeach

                </select>

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-primary btn-sm">

                Simpan

            </button>


            <a href="{{ url('admin/jadwal-mengajar') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection
