@extends('layouts.sidebar-admin')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Rekap Hasil Belajar Siswa
            </h5>

        </div>

        <div class="card-body">

            {{-- IDENTITAS --}}

            <table class="table table-bordered table-sm mb-4">

                <tr>

                    <th width="180">Nama</th>
                    <td>{{ $siswa->nama_lengkap }}</td>

                    <th width="120">NIS</th>
                    <td>{{ $siswa->nis }}</td>

                </tr>

                <tr>

                    <th>NISN</th>
                    <td>{{ $siswa->nisn }}</td>

                    <th>Kelas</th>
                    <td>
                        {{ $siswa->kelas->nama_kelas ?? '-' }}
                    </td>

                </tr>

            </table>

            {{-- NILAI AKADEMIK --}}

            <table class="table table-bordered table-sm">

                <thead>

                    <tr class="table-primary text-center">

                        <th rowspan="2" width="50">
                            No
                        </th>

                        <th rowspan="2">
                            Mata Pelajaran
                        </th>

                        <th colspan="2">
                            X
                        </th>

                        <th colspan="2">
                            XI
                        </th>

                        <th colspan="2">
                            XII
                        </th>

                    </tr>

                    <tr class="table-primary text-center">

                        <th>Ganjil</th>
                        <th>Genap</th>

                        <th>Ganjil</th>
                        <th>Genap</th>

                        <th>Ganjil</th>
                        <th>Genap</th>

                    </tr>

                </thead>

                <tbody>

                    <tr class="table-secondary">

                        <th colspan="8">
                            A. Kelompok Umum
                        </th>

                    </tr>

                    @foreach($mapelWajib as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item['nama_mapel'] }}
                        </td>

                        @foreach($item['nilai'] as $nilai)

                            <td class="text-center">
                                {{ $nilai }}
                            </td>

                        @endforeach

                    </tr>

                    @endforeach

                    <tr class="table-secondary">

                        <th colspan="8">
                            B. Kelompok Mata Pelajaran Pilihan
                        </th>

                    </tr>

                    @foreach($mapelPilihan as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item['nama_mapel'] }}
                        </td>

                        @foreach($item['nilai'] as $nilai)

                            <td class="text-center">
                                {{ $nilai }}
                            </td>

                        @endforeach

                    </tr>

                    @endforeach

                </tbody>

            </table>

            {{-- EKSKUL --}}

            <h6 class="mt-4 mb-2">
                Nilai Ekstrakurikuler
            </h6>

            <table class="table table-bordered table-sm">

                <thead>

                    <tr class="table-primary text-center">

                        <th rowspan="2">
                            No
                        </th>

                        <th rowspan="2">
                            Ekstrakurikuler
                        </th>

                        <th colspan="2">
                            X
                        </th>

                        <th colspan="2">
                            XI
                        </th>

                        <th colspan="2">
                            XII
                        </th>

                    </tr>

                    <tr class="table-primary text-center">

                        <th>Ganjil</th>
                        <th>Genap</th>

                        <th>Ganjil</th>
                        <th>Genap</th>

                        <th>Ganjil</th>
                        <th>Genap</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($nilaiEkskul as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item['nama'] }}
                        </td>

                        @foreach($item['nilai'] as $nilai)

                            <td class="text-center">
                                {{ $nilai }}
                            </td>

                        @endforeach

                    </tr>

                    @empty

                    <tr>

                        <td colspan="8"
                            class="text-center text-muted">

                            Belum ada nilai ekstrakurikuler

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            {{-- KEHADIRAN --}}

            <h6 class="mt-4 mb-2">
                Ketidakhadiran
            </h6>

            <table class="table table-bordered table-sm">

                <thead>

                    <tr class="table-primary text-center">

                        <th>Status</th>

                        <th>X Ganjil</th>
                        <th>X Genap</th>

                        <th>XI Ganjil</th>
                        <th>XI Genap</th>

                        <th>XII Ganjil</th>
                        <th>XII Genap</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Sakit</td>

                        @foreach($kehadiran['sakit'] as $item)

                            <td class="text-center">
                                {{ $item }}
                            </td>

                        @endforeach

                    </tr>

                    <tr>

                        <td>Izin</td>

                        @foreach($kehadiran['izin'] as $item)

                            <td class="text-center">
                                {{ $item }}
                            </td>

                        @endforeach

                    </tr>

                    <tr>

                        <td>Alpa</td>

                        @foreach($kehadiran['alpa'] as $item)

                            <td class="text-center">
                                {{ $item }}
                            </td>

                        @endforeach

                    </tr>

                </tbody>

            </table>

            {{-- KENAIKAN KELAS --}}

            <h6 class="mt-4 mb-2">
                Riwayat Kenaikan Kelas
            </h6>

            <table class="table table-bordered table-sm">

                <thead>

                    <tr>

                        <th>Tahun Ajaran</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($kenaikanKelas as $item)

                    <tr>

                        <td>
                            {{ $item->tahunAjaran->tahun ?? '-' }}
                        </td>

                        <td>

                            @if($item->status == 'naik')

                                <span class="badge bg-success">
                                    Naik Kelas
                                </span>

                            @elseif($item->status == 'lulus')

                                <span class="badge bg-primary">
                                    Lulus
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ ucfirst($item->status) }}
                                </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="2"
                            class="text-center text-muted">

                            Belum ada data

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="mt-3">

                <a href="{{ url('admin/rekap') }}"
                class="btn btn-secondary">
                    Kembali

                </a>

            </div>

        </div>

    </div>

</div>

@endsection