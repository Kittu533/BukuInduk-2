@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/kelasaktif-form.css') }}">
@endsection

@section('content')

<h3 class="page-title">Edit Kelas Aktif</h3>

<div class="card form-card">

    <div class="card-body">

        <form action="{{ url('admin/kelasaktif/'.$data->id_kelas_aktif) }}" method="POST">
            @csrf
            @method('PUT')

            
            <!-- KELAS -->
            <div class="mb-3">
                <label class="form-label">Kelas</label>

                <input type="text"
                    class="form-control"
                    value="{{ $data->kelas->nama_kelas ?? '-' }}"
                    readonly>

                <input type="hidden"
                    name="id_kelas"
                    value="{{ $data->id_kelas }}">
            </div>

            <!-- TAHUN AJARAN -->
            <div class="mb-3">
                <label class="form-label">Tahun Ajaran</label>

                 <input type="text"
                    class="form-control"
                    value="{{ $tahun->tahun }}"
                    readonly>
            </div>

            <!-- SEMESTER -->
            <div class="mb-3">

                <label class="form-label">
                    Semester Aktif
                </label>

                <input type="text"
                    class="form-control"
                    value="{{ $semester->nama_semester }}"
                    readonly>

            </div>

            <!-- WALI KELAS -->
            <div class="mb-3">
                <label class="form-label">Wali Kelas</label>

                <select name="id_guru" class="form-control" required>
                    @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}"
                        {{ $data->id_guru == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama_guru }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- BUTTON -->
            <button type="submit" class="btn btn-success">
                Update
            </button>

            <a href="{{ url('admin/kelasaktif') }}" class="btn btn-secondary">
                Kembali
            </a>

        </form>

    </div>

</div>

@endsection
