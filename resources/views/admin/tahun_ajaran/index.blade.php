@extends('layouts.sidebar-admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/tahun-ajaran.css') }}">
@endsection

@section('content')

<h3 class="page-title">DATA TAHUN AJARAN</h3>

@if(session('success'))
<script>
alert("{{ session('success') }}");
</script>
@endif


<div class="card table-card">

    <div class="card-body">

        <div class="mb-3">

            <form action="{{ url('admin/tahun-ajaran/auto') }}"
                method="POST"
                style="display:inline;">

                @csrf

                <button class="btn btn-success btn-sm">
                    + Tambah
                </button>

            </form>

        </div>


        <table class="table table-bordered table-striped">

            <thead>
                <tr>

                    <th width="60" class="text-center">
                        No
                    </th>

                    <th class="text-center">
                        Tahun Ajaran
                    </th>

                    <th class="text-center">
                        Status
                    </th>

                    <th width="220" class="text-center">
                        Aksi
                    </th>

                </tr>
            </thead>

            <tbody>

                @foreach($tahun as $t)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td class="text-center">
                        {{ $t->tahun }}
                    </td>

                    <td class="text-center">

                        @if(($t->status ?? null) == 'aktif')

                            <span class="badge bg-success">
                                Aktif
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Tidak Aktif
                            </span>

                        @endif

                    </td>

                    <td class="text-center">
                        <!-- AKTIFKAN -->
                        @if(($t->status ?? null) != 'aktif')

                        <form action="{{ url('admin/tahun-ajaran/aktif/'.$t->id_tahun) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf

                            <button class="btn btn-primary btn-sm">
                                Aktifkan
                            </button>

                        </form>

                        @endif

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
