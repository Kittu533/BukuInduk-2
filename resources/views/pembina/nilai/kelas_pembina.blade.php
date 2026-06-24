@extends('layouts.app-pembina')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h5 class="mb-0">
                {{ $ekskul->nama_ekskul }}
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th width="70">No</th>
                            <th>Kelas</th>
                            <th width="180">Status</th>
                            <th width="180">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($kelas as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->nama_kelas }}
                            </td>

                            <td>

                                @if(!$item->boleh_edit)

                                    @if($item->kelas_xii)

                                        <span class="badge bg-danger">
                                            Sudah Lulus
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Semester Ditutup
                                        </span>

                                    @endif

                                @else

                                    <span class="badge bg-success">
                                        Masih Bisa Dinilai
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($item->boleh_edit)

                                    <a href="{{ url('pembina/nilai/'.$ekskul->id_ekskul.'/'.$item->id_kelas_aktif) }}"
                                       class="btn btn-primary btn-sm">

                                        Input Nilai

                                    </a>

                                @else

                                    <a href="{{ url('pembina/nilai/'.$ekskul->id_ekskul.'/'.$item->id_kelas_aktif) }}"
                                       class="btn btn-secondary btn-sm">

                                        Lihat Nilai

                                    </a>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted">

                                Tidak ada data kelas.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <a href="{{ url('pembina/nilai') }}"
               class="btn btn-secondary btn-sm">

                Kembali

            </a>

        </div>

    </div>

</div>

@endsection