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
    EDIT KEHADIRAN
</h3>

<div class="card table-card">

    <div class="card-body">

        <form action="{{ url('admin/kehadiran/'.$data->id_kehadiran) }}"
              method="POST">

            @csrf
            @method('PUT')

            <input type="hidden"
                   name="id_siswa"
                   value="{{ $data->id_siswa }}">

            <div class="mb-3">

                <label class="form-label">
                    Nama Siswa
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data->nama_lengkap }}"
                       readonly>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Mata Pelajaran
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data->nama_mapel }}"
                       readonly>

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
                               value="{{ $data->tanggal }}"
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

                            <option value="sakit"
                                {{ $data->status == 'sakit' ? 'selected' : '' }}>

                                Sakit

                            </option>

                            <option value="izin"
                                {{ $data->status == 'izin' ? 'selected' : '' }}>

                                Izin

                            </option>

                            <option value="alpa"
                                {{ $data->status == 'alpa' ? 'selected' : '' }}>

                                Alpa

                            </option>

                        </select>

                    </div>

                </div>

            </div>

            <button type="submit"
                    class="btn btn-success btn-sm"
                    onclick="return confirm('Yakin ingin mengupdate data kehadiran ini?')">

                Update

            </button>

            <a href="{{ url()->previous() }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </form>

    </div>

</div>

@endsection