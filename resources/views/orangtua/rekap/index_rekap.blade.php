@extends('layouts.ortu-app')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="text-center fw-bold mb-4">
                REKAP HASIL BELAJAR SISWA
            </h3>

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <tr class="table-secondary">
                        <td colspan="2">
                            <strong>NAMA :</strong>
                            {{ strtoupper($siswa->nama_lengkap) }}
                        </td>

                        <td colspan="2" class="text-center">
                            {{ $tahunHeaders[0] ?? '....../......' }}
                        </td>

                        <td colspan="2" class="text-center">
                            {{ $tahunHeaders[1] ?? '....../......' }}
                        </td>

                        <td colspan="2" class="text-center">
                            {{ $tahunHeaders[2] ?? '....../......' }}
                        </td>
                    </tr>

                    <tr class="table-secondary">
                        <td colspan="2">
                            <strong>NISN :</strong>
                            {{ $siswa->nisn }}
                        </td>

                        <td colspan="2" class="text-center">SEMESTER</td>
                        <td colspan="2" class="text-center">SEMESTER</td>
                        <td colspan="2" class="text-center">SEMESTER</td>
                    </tr>

                    <tr class="table-secondary">
                        <td colspan="2">
                            <strong>NIS :</strong>
                            {{ $siswa->nis }}
                        </td>

                        <td class="text-center">Ganjil</td>
                        <td class="text-center">Genap</td>

                        <td class="text-center">Ganjil</td>
                        <td class="text-center">Genap</td>

                        <td class="text-center">Ganjil</td>
                        <td class="text-center">Genap</td>
                    </tr>

                    <tr class="table-dark text-center">
                        <th width="50">No</th>
                        <th>Mata Pelajaran</th>
                        <th>Nilai</th>
                        <th>Nilai</th>
                        <th>Nilai</th>
                        <th>Nilai</th>
                        <th>Nilai</th>
                        <th>Nilai</th>
                    </tr>

                    {{-- KELOMPOK UMUM --}}
                    <tr class="table-primary">
                        <td colspan="8">
                            <strong>A. Kelompok Umum</strong>
                        </td>
                    </tr>

                    @forelse($mapelWajib as $mapel)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $mapel['nama_mapel'] }}
                        </td>

                        @foreach($mapel['nilai'] as $nilai)
                            <td class="text-center">
                                {{ $nilai }}
                            </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Belum ada data
                        </td>
                    </tr>
                    @endforelse

                    {{-- KELOMPOK PILIHAN --}}
                    <tr class="table-primary">
                        <td colspan="8">
                            <strong>B. Kelompok Mapel Pilihan</strong>
                        </td>
                    </tr>

                    @forelse($mapelPilihan as $mapel)
                    <tr>
                        <td class="text-center">
                            {{ count($mapelWajib) + $loop->iteration }}
                        </td>

                        <td>
                            {{ $mapel['nama_mapel'] }}
                        </td>

                        @foreach($mapel['nilai'] as $nilai)
                            <td class="text-center">
                                {{ $nilai }}
                            </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Belum ada data
                        </td>
                    </tr>
                    @endforelse

                    <tr class="table-secondary">

                        <th colspan="2">
                            KENAIKAN / KELULUSAN
                        </th>

                        <td colspan="2" class="text-center">
                            {{ isset($naikKelas[0]) ? 'Naik Ke Kelas XI' : '-' }}
                        </td>

                        <td colspan="2" class="text-center">
                            {{ isset($naikKelas[1]) ? 'Naik Ke Kelas XII' : '-' }}
                        </td>

                        <td colspan="2" class="text-center">
                            {{ $lulus ? 'LULUS' : '-' }}
                        </td>

                    </tr>

                </table>

                <div class="mt-3">

                    <a href="{{ url('orangtua/dashboard') }}"
                    class="btn btn-secondary btn-sm">
                        Kembali
                    </a>

                </div>

            </div>

        </div>
    </div>

</div>
@endsection