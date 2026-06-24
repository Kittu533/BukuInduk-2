@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                Anggota Ekskul

                <span class="text-primary">
                    {{ $ekskul->nama_ekskul }}
                </span>

                <span class="badge bg-info ms-2">
                    {{ $siswa->count() }} Siswa
                </span>

            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="70" class="text-center">
                                No
                            </th>

                            <th width="120">
                                ID Siswa
                            </th>

                            <th>
                                Nama Siswa
                            </th>

                            <th width="150" class="text-center">
                                Kelas
                            </th>

                            <th width="120" class="text-center">
                                Nilai
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($siswa as $item)

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->siswa->id_siswa ?? '-' }}
                            </td>

                            <td>
                                {{ $item->siswa->nama_lengkap ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $item->kelasAktif->kelas->nama_kelas ?? '-' }}
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

                                @else

                                    <span class="badge bg-warning text-dark">
                                        C
                                    </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted">

                                Tidak ada anggota ekskul.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <a href="{{ url('pembina/siswa') }}"
               class="btn btn-secondary btn-sm mt-2">

                Kembali

            </a>

        </div>

    </div>

</div>

@endsection