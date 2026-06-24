@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/jadwal-mengajar.css') }}">
@endsection


@section('content')

<h3 class="page-title">
    DETAIL JADWAL MENGAJAR
</h3>


<div class="card table-card">

    <div class="card-body">


        <!-- INFORMASI GURU -->
        <div class="mb-4">

            <h5 class="mb-1">

                {{ $detail[0]->nama_guru }}

            </h5>


            <span class="text-muted">

                Mata Pelajaran :

                {{ $detail[0]->id_mapel }}
                -
                {{ $detail[0]->nama_mapel }}

                ({{ $detail[0]->kategori_mapel }} - {{ data_get($detail[0], 'semester_mapel') ?? '-' }})

            </span>

        </div>


        <!-- TABEL -->
        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th width="60" class="text-center">
                        No
                    </th>

                    <th class="text-center">
                        Kelas
                    </th>

                    <th width="220" class="text-center">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($detail as $d)

                <tr>

                    <td class="text-center">

                        {{ $loop->iteration }}

                    </td>


                    <td class="text-center">

                        {{ $d->nama_kelas }}

                    </td>


                    <td class="text-center">

                        <a href="{{ url('admin/jadwal-mengajar/'.$d->id_jadwal.'/edit') }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>


                        <form action="{{ url('admin/jadwal-mengajar/'.$d->id_jadwal) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin hapus data?')">

                                Hapus

                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>


        <a href="{{ url('admin/jadwal-mengajar') }}"
           class="btn btn-secondary btn-sm mt-3">

            Kembali

        </a>

    </div>

</div>

@endsection
