<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Belajar - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 25px 30px; size: A4 portrait; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        h2.title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 14px;
            letter-spacing: 0.5px;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.main th, table.main td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9.5px;
            vertical-align: middle;
        }

        /* Header info siswa (atas) */
        .siswa-info td {
            background: #d9d9d9;
            font-weight: bold;
            text-transform: uppercase;
        }
        .siswa-info td.tahun-cell {
            text-align: center;
        }

        /* Baris semester */
        .semester-row td {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Header kolom NO/MAPEL */
        .col-header td {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .col-header td.text-left { text-align: left; }

        /* Group title (Kelompok Umum, Kelompok Mapel Pilihan) */
        .group-row td {
            background: #d9d9d9;
            font-weight: bold;
        }

        /* Cell biasa */
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        /* Kenaikan/Kelulusan */
        .kenaikan-label td {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            padding: 12px;
        }

        .ttd {
            margin-top: 18px;
            width: 100%;
            text-align: right;
            font-size: 10px;
            page-break-inside: avoid;
        }
        .ttd p {
            text-align: right;
        }
        .ttd .name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    {{-- ====================== JUDUL ====================== --}}
    <h2 class="title">HASIL BELAJAR SISWA</h2>

    {{-- ====================== TABEL HEADER + MAPEL ====================== --}}
    <table class="main">
        {{-- Header siswa & tahun --}}
        <tr class="siswa-info">
            <td colspan="2" style="width:50%;">NAMA : {{ strtoupper($siswa->nama_lengkap) }}</td>
            <td class="tahun-cell" colspan="2">{{ $tahunHeaders[0] ?? '....../......' }}</td>
            <td class="tahun-cell" colspan="2">{{ $tahunHeaders[1] ?? '....../......' }}</td>
            <td class="tahun-cell" colspan="2">{{ $tahunHeaders[2] ?? '....../......' }}</td>
        </tr>
        <tr class="siswa-info">
            <td colspan="2">NISN : {{ $siswa->nisn ?? '-' }}</td>
            <td class="tahun-cell" colspan="2">SEMESTER</td>
            <td class="tahun-cell" colspan="2">SEMESTER</td>
            <td class="tahun-cell" colspan="2">SEMESTER</td>
        </tr>
        <tr class="semester-row">
            <td colspan="2" style="text-align:left; text-transform:uppercase;">NIS : {{ $siswa->nis ?? '-' }}</td>
            <td>Ganjil</td>
            <td>Genap</td>
            <td>Ganjil</td>
            <td>Genap</td>
            <td>Ganjil</td>
            <td>Genap</td>
        </tr>

        {{-- Header kolom mapel --}}
        <tr class="col-header">
            <td style="width:30px;">NO</td>
            <td class="text-left">MATA PELAJARAN</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
        </tr>

        {{-- Kelompok Umum --}}
        <tr class="group-row">
            <td colspan="8">Kelompok Umum</td>
        </tr>
        @foreach($mapelWajib as $idx => $mapel)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-left">{{ $mapel->nama_mapel }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][0] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][1] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][2] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][3] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][4] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][5] ?? '-' }}</td>
        </tr>
        @endforeach

        {{-- Kelompok Mapel Pilihan --}}
        <tr class="group-row">
            <td colspan="8">Kelompok Mapel Pilihan</td>
        </tr>
        @foreach($mapelPilihan as $idx => $mapel)
        <tr>
            <td class="text-center">{{ count($mapelWajib) + $idx + 1 }}</td>
            <td class="text-left">{{ $mapel->nama_mapel }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][0] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][1] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][2] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][3] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][4] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiPerMapel[$mapel->id_mapel][5] ?? '-' }}</td>
        </tr>
        @endforeach
    </table>

    {{-- ====================== EKSTRAKURIKULER ====================== --}}
    <table class="main">
        <tr class="col-header">
            <td style="width:30px;">NO</td>
            <td class="text-left">EKSTRAKURIKULER</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
            <td style="width:50px;">NILAI</td>
        </tr>
        @forelse($daftarEkskul as $idx => $ekskul)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-left">{{ $ekskul->nama_ekskul }}</td>
            <td class="text-center">{{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][0] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][1] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][2] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][3] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][4] ?? '-' }}</td>
            <td class="text-center">{{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][5] ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td class="text-center">1</td>
            <td class="text-left">-</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
        </tr>
        @endforelse
    </table>

    {{-- ====================== KETIDAKHADIRAN ====================== --}}
    <table class="main">
        <tr class="col-header">
            <td style="width:30px;">NO</td>
            <td class="text-left">KETIDAKHADIRAN</td>
            <td style="width:50px;">HARI</td>
            <td style="width:50px;">HARI</td>
            <td style="width:50px;">HARI</td>
            <td style="width:50px;">HARI</td>
            <td style="width:50px;">HARI</td>
            <td style="width:50px;">HARI</td>
        </tr>
        <tr>
            <td class="text-center">1.</td>
            <td class="text-left">Sakit</td>
            @for($i = 0; $i < 6; $i++)
                <td class="text-center">{{ $kehadiranPerSemester[$i]['sakit'] ?? '-' }}</td>
            @endfor
        </tr>
        <tr>
            <td class="text-center">2.</td>
            <td class="text-left">Izin</td>
            @for($i = 0; $i < 6; $i++)
                <td class="text-center">{{ $kehadiranPerSemester[$i]['izin'] ?? '-' }}</td>
            @endfor
        </tr>
        <tr>
            <td class="text-center">3.</td>
            <td class="text-left">Tanpa Keterangan</td>
            @for($i = 0; $i < 6; $i++)
                <td class="text-center">{{ $kehadiranPerSemester[$i]['alpa'] ?? '-' }}</td>
            @endfor
        </tr>
    </table>

    {{-- ====================== KENAIKAN / KELULUSAN ====================== --}}
    <table class="main">
        <tr>
            <td class="kenaikan-label" colspan="2" style="width:50%;">KENAIKAN / KELULUSAN</td>
            <td class="text-center" colspan="2" style="font-weight:bold; padding:12px; line-height:1.4;">
                {{ $kenaikan[0] ?? '-' }}
            </td>
            <td class="text-center" colspan="2" style="font-weight:bold; padding:12px; line-height:1.4;">
                {{ $kenaikan[1] ?? '-' }}
            </td>
            <td class="text-center" colspan="2" style="font-weight:bold; padding:12px; line-height:1.4;">
                {{ $kenaikan[2] ?? '-' }}
            </td>
        </tr>
    </table>

    {{-- ====================== TTD ====================== --}}
    <div class="ttd">
        <p style="margin:0;">Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p style="margin:0;">Kepala Sekolah</p>
        <br><br><br>
        <p class="name" style="margin:0;">{{ $profil->kepala_sekolah ?? 'Sumarsono' }}</p>
        <p style="margin:0; font-weight:bold;">NIP. {{ $profil->nip_kepala_sekolah ?? '196707121994121006' }}</p>
    </div>

</body>
</html>
