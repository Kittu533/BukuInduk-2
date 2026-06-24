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
    DATA SISWA
</h3>

<div class="card table-card">

    <div class="card-body">

        <div class="mb-3">

            <h5>
                Kelas : {{ $kelas->nama_kelas }}
            </h5>

        </div>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th class="text-center" width="60">
                        No
                    </th>

                    <th class="text-center">
                        NIS
                    </th>

                    <th>
                        Nama
                    </th>

                    <th class="text-center">
                        Jenis Kelamin
                    </th>

                    <th class="text-center" width="180">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($siswa as $s)

                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td class="text-center">
                            {{ $s->nis }}
                        </td>

                        <td>
                            {{ $s->nama_lengkap }}
                        </td>

                        <td class="text-center">
                            {{ $s->jenis_kelamin }}
                        </td>

                        <td class="text-center">

                            <a href="{{ url('admin/nilai_akademik/detail/'.$s->id_siswa) }}"
                               class="btn btn-primary btn-sm">
                                Detail
                            </a>

                            <a href="{{ url('admin/export/hasil-belajar/'.$s->id_siswa) }}"
                               class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Rapor
                            </a>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <a href="{{ url('admin/nilai_akademik') }}"
           class="btn btn-secondary btn-sm mt-3">

            Kembali

        </a>

    </div>

</div>

@endsection