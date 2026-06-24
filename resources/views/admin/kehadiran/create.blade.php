@extends('layouts.sidebar-admin')

@section('content')

@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show"
         role="alert">

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">

        </button>

    </div>

@endif

<h3 class="page-title">
    TAMBAH KEHADIRAN
</h3>

<div class="card table-card">

    <div class="card-body">

        <form action="{{ url('admin/kehadiran/store') }}"
              method="POST">

            @csrf

            <input type="hidden"
                   name="id_siswa"
                   value="{{ $siswa->id_siswa }}">

            <div class="mb-3">

                <label class="form-label">
                    Nama Siswa
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $siswa->nama_lengkap }}"
                       readonly>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Kelas
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $kelas->nama_kelas }}"
                       readonly>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Mata Pelajaran
                </label>

                <select name="id_jadwal"
                        class="form-control"
                        required>

                    <option value="">
                        -- Pilih Mata Pelajaran --
                    </option>

                    @foreach($jadwal as $j)

                        <option value="{{ $j->id_jadwal }}">

                            {{ $j->nama_mapel }}
                            -
                            {{ $j->nama_guru }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label">
                            Tanggal
                        </label>

                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               required>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status"
                                class="form-control"
                                required>

                            <option value="">
                                -- Pilih Status --
                            </option>

                            <option value="sakit">
                                Sakit
                            </option>

                            <option value="izin">
                                Izin
                            </option>

                            <option value="alpa">
                                Alpa
                            </option>

                        </select>

                    </div>

                </div>

            </div>

            <button type="submit"
                    class="btn btn-success btn-sm"
                    onclick="return confirm('Data berhasil di simpan')">

                Simpan
            </button>

            <a href="{{ url()->previous() }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection