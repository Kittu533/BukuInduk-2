@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/nilai-ekskul-detail.css') }}">
@endsection

@section('content')

<h3 class="page-title">
    EDIT NILAI EKSTRAKURIKULER
</h3>

<div class="card form-card">

    <div class="card-body">

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

        <form action="{{ url('admin/nilai_ekskul/update_siswa/'.$id) }}"
              method="POST">

            @csrf

            <!-- DATA SISWA -->

            @if(count($data) > 0)

            <div class="mb-4">

                <label class="form-label fw-bold">
                    Nama Siswa
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data[0]->nama_lengkap }}"
                       readonly>

            </div>

            <div class="mb-4">

                <label class="form-label fw-bold">
                    NIS
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $data[0]->nis }}"
                       readonly>

            </div>

            @endif

            <!-- NILAI EKSKUL -->

            @foreach($data as $d)

            <div class="mb-3">

                <label class="form-label">

                    {{ $d->nama_ekskul }}

                </label>

                <select name="nilai[{{ $d->id_nilai_ekskul }}]"
                        class="form-select">

                    <option value="">
                        -- Pilih Nilai --
                    </option>

                    <option value="A"
                        {{ $d->nilai == 'A' ? 'selected' : '' }}>
                        A
                    </option>

                    <option value="B"
                        {{ $d->nilai == 'B' ? 'selected' : '' }}>
                        B
                    </option>

                    <option value="C"
                        {{ $d->nilai == 'C' ? 'selected' : '' }}>
                        C
                    </option>

                </select>

            </div>

            @endforeach

            <div class="mt-4">

                <button type="submit"
                        class="btn btn-success btn-sm">

                    Update

                </button>

                <a href="{{ url('admin/nilai_ekskul/detail/'.$id) }}"
                   class="btn btn-secondary btn-sm">

                    Kembali

                </a>

            </div>

        </form>

    </div>

</div>

@endsection