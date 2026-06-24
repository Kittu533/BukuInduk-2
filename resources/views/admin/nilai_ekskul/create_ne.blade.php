@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/nilai-ekskul-detail.css') }}">
@endsection

@section('content')

<h3 class="page-title">
    TAMBAH NILAI EKSTRAKURIKULER
</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/nilai_ekskul/store') }}"
              method="POST">

            @csrf

            <input type="hidden"
                   name="id_siswa"
                   value="{{ $siswa->id_siswa }}">

            <input type="hidden"
                   name="id_kelas_aktif"
                   value="{{ $siswa->id_kelas_aktif }}">

            <!-- DATA SISWA -->

            <div class="mb-3">

                <label class="form-label fw-bold">
                    Nama Siswa
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $siswa->nama_lengkap }}"
                       readonly>

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    NIS
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $siswa->nis }}"
                       readonly>

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    Kelas
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $siswa->nama_kelas }}"
                       readonly>

            </div>

            <!-- EKSKUL -->

            <div class="mb-3">

                <label class="form-label fw-bold">
                    Ekstrakurikuler
                </label>

                <select name="id_ekskul"
                        class="form-select"
                        required>

                    <option value="">
                        -- Pilih Ekstrakurikuler --
                    </option>

                    @foreach($ekskul as $e)

                    <option value="{{ $e->id_ekskul }}">

                        {{ $e->nama_ekskul }}

                    </option>

                    @endforeach

                </select>

            </div>

            <!-- NILAI -->

            <div class="mb-4">

                <label class="form-label fw-bold">
                    Nilai
                </label>

                <select name="nilai"
                        class="form-select"
                        required>

                    <option value="">
                        -- Pilih Nilai --
                    </option>

                    <option value="A">
                        A
                    </option>

                    <option value="B">
                        B
                    </option>

                    <option value="C">
                        C
                    </option>

                </select>

            </div>

            <button type="submit"
                    class="btn btn-success btn-sm">

                Simpan

            </button>

            <a href="{{ url('admin/nilai_ekskul/detail/'.$siswa->id_siswa) }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection