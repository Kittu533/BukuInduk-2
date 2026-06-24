@extends('layouts.sidebar-walas')

@section('css')
<link rel="stylesheet" href="{{ asset('css/siswa.css') }}">
@endsection

@section('content')

<div class="container bg-white p-4 rounded shadow-sm">


    <h4 class="text-center fw-bold mb-4">
        BUKU INDUK SISWA SMA NEGERI 3 CILACAP
    </h4>

    <table class="table table-borderless mb-3">

    <tr>

        <td width="33%">
            <b>Nomor Induk</b> : {{ $siswa->nis }}
        </td>

        <td width="33%" class="text-center">
            <b>NISN</b> : {{ $siswa->nisn }}
        </td>

        <td width="33%" class="text-end">
            <b>Thn Masuk</b> :
            {{ \Carbon\Carbon::parse($siswa->tahun_masuk)->translatedFormat('d F Y') }}
        </td>

    </tr>

    </table>

    <div class="row">

        {{-- DATA SISWA --}}
        <div class="col-md-9">

            <h6 class="section-title fw-bold mb-3">A. KETERANGAN SISWA</h6>

            <table class="table table-borderless table-sm data-table">

                <tr>
                    <td width="220">Nama Siswa</td>
                    <td>: {{ $siswa->nama_lengkap }}</td>
                </tr>

                <tr>
                    <td>Jenis Kelamin</td>
                    <td>: {{ $siswa->jenis_kelamin }}</td>
                </tr>

                <tr>
                    <td>Kelahiran</td>
                    <td></td>
                </tr>

                <tr>
                    <td class="sub">a. Tempat</td>
                    <td>: {{ $siswa->tempat_lahir }}</td>
                </tr>

                <tr>
                    <td class="sub">b. Tanggal</td>
                    <td>
                        : {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}
                    </td>
                </tr>

                <tr>
                    <td>Agama</td>
                    <td>: {{ $siswa->agama }}</td>
                </tr>

                <tr>
                    <td>Status dalam Keluarga</td>
                    <td>: {{ $siswa->status_keluarga }}</td>
                </tr>

                <tr>
                    <td>Anak ke</td>
                    <td>: {{ $siswa->anak_ke }}</td>
                </tr>

                <tr>
                    <td>Alamat</td>
                    <td>: {{ $siswa->alamat }}</td>
                </tr>

                <tr>
                    <td>Nomor Telepon</td>
                    <td>: {{ $siswa->no_telp }}</td>
                </tr>

            </table>


            <h6 class="section-title fw-bold mt-4">
                B. KETERANGAN ORANG TUA / WALI SISWA
            </h6>

            <table class="table table-borderless table-sm data-table">

                <tr>
                    <td width="220">Nama Orang Tua</td>
                    <td></td>
                </tr>

                <tr>
                    <td class="sub">a. Ayah</td>
                    <td>: {{ $siswa->nama_ayah }}</td>
                </tr>

                <tr>
                    <td class="sub">b. Ibu</td>
                    <td>: {{ $siswa->nama_ibu }}</td>
                </tr>


                <tr>
                    <td>Pekerjaan Orang Tua</td>
                    <td></td>
                </tr>

                <tr>
                    <td class="sub">a. Ayah</td>
                    <td>: {{ $siswa->pekerjaan_ayah }}</td>
                </tr>

                <tr>
                    <td class="sub">b. Ibu</td>
                    <td>: {{ $siswa->pekerjaan_ibu }}</td>
                </tr>

                <tr>
                    <td>Alamat Orang Tua</td>
                    <td>: {{ $siswa->alamat_ortu }}</td>
                </tr>

                <tr>
                    <td>Telepon Orang Tua</td>
                    <td>: {{ $siswa->no_telp_ortu }}</td>
                </tr>


                <tr>
                    <td>Wali Siswa</td>
                    <td></td>
                </tr>

                <tr>
                    <td class="sub">a. Nama</td>
                    <td>: {{ $siswa->nama_wali }}</td>
                </tr>

                <tr>
                    <td class="sub">b. Pekerjaan</td>
                    <td>: {{ $siswa->pekerjaan_wali }}</td>
                </tr>

                <tr>
                    <td class="sub">c. Alamat</td>
                    <td>: {{ $siswa->alamat_wali }}</td>
                </tr>

                <tr>
                    <td class="sub">d. Telepon</td>
                    <td>: {{ $siswa->no_telp_wali }}</td>
                </tr>

            </table>


            <h6 class="section-title fw-bold mt-4">
                C. PERKEMBANGAN SISWA
            </h6>

            <table class="table table-borderless table-sm data-table">

                <tr>
                    <td width="220">Sekolah Asal</td>
                    <td>: {{ $siswa->asal_sekolah }}</td>
                </tr>

                <tr>
                    <td>Diterima di sekolah ini</td>
                    <td></td>
                </tr>

                <tr>
                    <td class="sub">a. Di kelas</td>
                    <td>: {{ $siswa->nama_kelas }}</td>
                </tr>

                <tr>
                    <td class="sub">b. Pada Tanggal</td>
                    <td>
                        : {{ \Carbon\Carbon::parse($siswa->tahun_masuk)->translatedFormat('d F Y') }}
                    </td>
                </tr>

</table>

        </div>


        {{-- FOTO --}}
        <div class="col-md-3 text-center">

            <div class="foto-frame">

                @if(isset($siswa->foto) && $siswa->foto != '')

                    <img src="{{ asset('foto/'.$siswa->foto) }}">

                @else

                    Foto<br>3 x 4

                @endif

            </div>

        </div>

    </div>


    <div class="text-end mt-5">

        <p>
            Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            <br>
            Kepala Sekolah
        </p>

        <br><br>

        <b>Sumarsono</b>
        <br>
        NIP. 196707121994121006

    </div>

    <div class="mt-3">
                <a href="{{ url('/wali/siswa') }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
    </div>

</div>


@endsection
