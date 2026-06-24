@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    REKAP NILAI (BUKU INDUK)
</h3>

<div class="card table-card">

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th width="60" class="text-center">No</th>
                    <th class="text-center">Kelas</th>
                    <th class="text-center">Wali Kelas</th>
                    <th class="text-center">Tahun Ajaran</th>
                    <th class="text-center">Semester</th>
                    <th class="text-center">Jumlah Siswa</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($kelas as $k)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $k->kelas->nama_kelas }}
                    </td>

                    <td>
                        {{ $k->guru->nama_guru }}
                    </td>

                    <td>
                        {{ $k->tahunAjaran->tahun }}
                    </td>

                    <td>
                        {{ $k->semester->nama_semester }}
                    </td>

                    <td class="text-center">
                        {{ $k->jumlah_siswa }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/rekap/'.$k->id_kelas_aktif) }}"
                        class="btn btn-info btn-sm">
                            Detail
                        </a>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection