@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/nilai-ekskul-detail.css') }}">
@endsection

@section('content')

<h3 class="page-title">
    DETAIL NILAI EKSTRAKURIKULER
</h3>

<div class="card table-card">

    <div class="card-body">

        <div class="mb-3">

            <h5>
                Kelas : {{ $kelas->nama_kelas }}
            </h5>

        </div>

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th class="text-center" width="50">
                        No
                    </th >

                    <th class="text-center">
                        Nama Siswa
                    </th>

                    <th class="text-center" width="180">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($siswa as $d)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $d->nama_lengkap }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/nilai_ekskul/detail/'.$d->id_siswa) }}"
                           class="btn btn-info btn-sm">

                            Detail

                        </a>

                    </td>
                    

                </tr>

                @endforeach

            </tbody>

        </table>

            <a href="{{ url('admin/nilai_ekskul') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

    </div>

</div>

@endsection