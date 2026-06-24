@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/guru.css') }}">
@endsection


@section('content')

<h3 class="page-title">DETAIL DATA GURU</h3>

<div class="card detail-card">

    <div class="card-body">

        <table class="table table-bordered table-detail">

            <tr>

                <th>
                    ID Guru
                </th>

                <td>
                    {{ $guru->id_guru }}
                </td>

            </tr>


            <tr>

                <th>
                    NIP
                </th>

                <td>
                    {{ $guru->nip }}
                </td>

            </tr>


            <tr>

                <th>
                    Nama Guru
                </th>

                <td>
                    {{ $guru->nama_guru }}
                </td>

            </tr>


            <tr>

                <th>
                    Jenis Kelamin
                </th>

                <td>
                    {{ $guru->jenis_kelamin }}
                </td>

            </tr>


            <tr>

                <th>
                    Jabatan
                </th>

                <td>
                    {{ $guru->jabatan }}
                </td>

            </tr>


            <tr>

                <th>
                    Tugas Mengajar
                </th>

                <td>
                    {{ $guru->tugas_mengajar }}
                </td>

            </tr>

        </table>


        <div class="mt-3">

            <a href="{{ url('admin/guru') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </div>

    </div>

</div>

@endsection