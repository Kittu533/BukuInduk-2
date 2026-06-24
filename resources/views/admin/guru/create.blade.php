@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
@endsection


@section('content')

<h3 class="page-title">TAMBAH GURU</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/guru') }}"
              method="POST">

            @csrf


            <!-- ID GURU -->
            <div class="mb-3">

                <label class="form-label">
                    ID Guru
                </label>

                <input type="text"
                       name="id_guru"
                       class="form-control"
                       placeholder="Contoh: HRH"
                       required>

            </div>


            <!-- NAMA GURU -->
            <div class="mb-3">

                <label class="form-label">
                    Nama Guru
                </label>

                <input type="text"
                       name="nama_guru"
                       class="form-control"
                       placeholder="Masukkan nama guru"
                       required>

            </div>


            <!-- NIP -->
            <div class="mb-3">

                <label class="form-label">
                    NIP
                </label>

                <input type="text"
                       name="nip"
                       class="form-control"
                       placeholder="Masukkan NIP">

            </div>


            <!-- JENIS KELAMIN -->
            <div class="mb-3">

                <label class="form-label">
                    Jenis Kelamin
                </label>

                <select name="jenis_kelamin"
                        class="form-control">

                    <option value="">
                        -- Pilih Jenis Kelamin --
                    </option>

                    <option value="Laki-laki">
                        Laki-laki
                    </option>

                    <option value="Perempuan">
                        Perempuan
                    </option>

                </select>

            </div>


            <!-- JABATAN -->
            <div class="mb-3">

                <label class="form-label">
                    Jabatan
                </label>

                <input type="text"
                       name="jabatan"
                       class="form-control"
                       placeholder="Contoh: Kepala Sekolah">

            </div>


            <!-- TUGAS MENGAJAR -->
            <div class="mb-3">

                <label class="form-label">
                    Tugas Mengajar
                </label>

                <input type="text"
                       name="tugas_mengajar"
                       class="form-control"
                       placeholder="Contoh: Matematika">

            </div>


            <!-- BUTTON -->
            <button type="submit"
                    class="btn btn-primary btn-sm">

                Simpan

            </button>


            <a href="{{ url('admin/guru') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection