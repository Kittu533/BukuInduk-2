@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm mb-3">

        <div class="card-header">

            <h5 class="mb-0">
                Riwayat Nilai Ekskul
                <span class="text-primary">
                    {{ $ekskul->nama_ekskul }}
                </span>
            </h5>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="70">
                                No
                            </th>

                            <th width="140">
                                ID Siswa
                            </th>

                            <th>
                                Nama Siswa
                            </th>

                            <th width="120" class="text-center">
                                Nilai
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($siswa as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->id_siswa }}
                            </td>

                            <td>
                                {{ $item->nama_lengkap }}
                            </td>

                            <td class="text-center">

                                @if($item->nilai == 'A')

                                    <span class="badge bg-success">
                                        A
                                    </span>

                                @elseif($item->nilai == 'B')

                                    <span class="badge bg-primary">
                                        B
                                    </span>

                                @elseif($item->nilai == 'C')

                                    <span class="badge bg-warning text-dark">
                                        C
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted">

                                Tidak ada data nilai ekstrakurikuler.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <a href="{{ url('pembina/riwayat-nilai') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </div>

</div>

@endsection