@extends('layouts.sidebar-admin')

@section('content')

<h3 class="page-title">
    DATA SISWA KELAS {{ $kelas->nama_kelas }}
</h3>

<div class="card table-card">

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>

                <tr>
                    <th width="60" class="text-center">No</th>
                    <th width="120" class="text-center">NIS</th>
                    <th class="text-center">Nama Siswa</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>

            </thead>

            <tbody>

                @foreach($siswa as $item)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td class="text-center">
                        {{ $item->siswa->nis }}
                    </td>

                    <td>
                        {{ $item->siswa->nama_lengkap }}
                    </td>

                    <td class="text-center">

                        <a href="{{ url('admin/kenaikan/siswa/'.$item->siswa->id_siswa) }}"
                           class="btn btn-primary btn-sm">

                            Kelola Status

                        </a>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

        <div class="mb-3">

            <a href="{{ url('admin/kenaikan') }}"
            class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </div>

    </div>

</div>

@endsection