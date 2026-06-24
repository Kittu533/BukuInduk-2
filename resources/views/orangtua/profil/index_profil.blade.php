@extends('layouts.ortu-app')

@section('css')
<link rel="stylesheet"
      href="{{ asset('css/orangtua/profil.css') }}">
@endsection

@section('content')

<div class="profil-wrapper">

    {{-- STATUS AKADEMIK --}}
    <div class="section-block">

        <h5 class="section-title">
            STATUS AKADEMIK
        </h5>

        <table class="table table-bordered">

            <tr>
                <th width="250">Kelas Aktif</th>
                <td>{{ $kelasSiswa->nama_kelas ?? '-' }}</td>
            </tr>

            <tr>
                <th>Tahun Ajaran</th>
                <td>{{ $kelasSiswa->tahun ?? '-' }}</td>
            </tr>

            <tr>
                <th>Semester</th>
                <td>{{ $kelasSiswa->nama_semester ?? '-' }}</td>
            </tr>

            <tr>
                <th>Wali Kelas</th>
                <td>{{ $kelasSiswa->nama_guru ?? '-' }}</td>
            </tr>

        </table>

    </div>

    {{-- DATA PRIBADI --}}
    <div class="section-block">

        <h5 class="section-title">
            DATA PRIBADI SISWA
        </h5>

        <table class="table table-bordered">

            <tr>
                <th width="250">NIS</th>
                <td>{{ $siswa->nis ?? '-' }}</td>
            </tr>

            <tr>
                <th>NISN</th>
                <td>{{ $siswa->nisn ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $siswa->nama_lengkap ?? '-' }}</td>
            </tr>

            <tr>
                <th>Jenis Kelamin</th>
                <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
            </tr>

            <tr>
                <th>Agama</th>
                <td>{{ $siswa->agama ?? '-' }}</td>
            </tr>

            <tr>
                <th>Tempat Lahir</th>
                <td>{{ $siswa->tempat_lahir ?? '-' }}</td>
            </tr>

            <tr>
                <th>Tanggal Lahir</th>
                <td>{{ $siswa->tanggal_lahir ?? '-' }}</td>
            </tr>

            <tr>
                <th>Alamat</th>
                <td>{{ $siswa->alamat ?? '-' }}</td>
            </tr>

        </table>

    </div>

    {{-- DATA ORANG TUA --}}
    <div class="section-block">

        <h5 class="section-title">
            DATA ORANG TUA
        </h5>

        <table class="table table-bordered">

            <tr>
                <th width="250">Nama Ayah</th>
                <td>{{ $siswa->nama_ayah ?? '-' }}</td>
            </tr>

            <tr>
                <th>Pekerjaan Ayah</th>
                <td>{{ $siswa->pekerjaan_ayah ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nama Ibu</th>
                <td>{{ $siswa->nama_ibu ?? '-' }}</td>
            </tr>

            <tr>
                <th>Pekerjaan Ibu</th>
                <td>{{ $siswa->pekerjaan_ibu ?? '-' }}</td>
            </tr>

            <tr>
                <th>No HP Orang Tua</th>
                <td>{{ $siswa->no_hp_ortu ?? '-' }}</td>
            </tr>

        </table>

    </div>

    {{-- KEAMANAN --}}
    <div class="section-block">

        <h5 class="section-title">
            KEAMANAN AKUN
        </h5>

        <table class="table table-bordered">

            <tr>
                <th width="250">Username</th>
                <td>{{ $user->username ?? '-' }}</td>
            </tr>

            <tr>
                <th>Password</th>
                <td>••••••••••••</td>
            </tr>

        </table>

        <div class="text-end mt-3">

            <a href="{{ url('orangtua/profil/password') }}"
               class="btn btn-primary">

                <i class="bi bi-key-fill me-2"></i>
                Ubah Password

            </a>

        </div>

    </div>

</div>

@endsection