<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 18px 25px; size: A4 portrait; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        h2.title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px;
            letter-spacing: 0.5px;
        }

        /* ============ INFO SISWA ============ */
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.info td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: middle;
        }
        table.info td.label {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
            width: 90px;
        }
        table.info td.value {
            padding: 4px 8px;
        }

        /* ============ SECTION TITLE ============ */
        .section {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin: 8px 0 4px;
        }

        /* ============ TABLE DATA ============ */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 10.5px;
            vertical-align: middle;
        }
        table.data th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table.data td.text-center { text-align: center; }
        table.data td.text-left { text-align: left; }

        /* ============ TTD ============ */
        .ttd {
            margin-top: 12px;
            font-size: 11px;
        }
        .ttd table { width: 100%; border-collapse: collapse; }
        .ttd table td { border: none; padding: 2px 4px; vertical-align: top; }
    </style>
</head>
<body>

    {{-- ====================== JUDUL ====================== --}}
    <h2 class="title">LAPORAN HASIL BELAJAR</h2>

    {{-- ====================== INFO SISWA (2 kolom dalam 1 tabel) ====================== --}}
    <table class="info">
        <tr>
            <td class="label">Nama</td>
            <td class="value" style="width:35%;">: {{ strtoupper($siswa->nama_lengkap) }}</td>
            <td class="label">NIS</td>
            <td class="value" style="width:15%;">: {{ $siswa->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="value">: {{ $kelasData?->kelasAktif?->kelas?->nama_kelas ?? '-' }}</td>
            <td class="label">Semester</td>
            <td class="value">: {{ $kelasData?->kelasAktif?->semester?->nama_semester ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td class="value">: {{ $kelasData?->kelasAktif?->semester?->tahunAjaran?->tahun ?? '-' }}</td>
            <td class="label">NISN</td>
            <td class="value">: {{ $siswa->nisn ?? '-' }}</td>
        </tr>
    </table>

    {{-- ====================== A. NILAI AKADEMIK ====================== --}}
    <div class="section">A. Nilai Akademik</div>
    <table class="data">
        <thead>
            <tr>
                <th width="35">No</th>
                <th class="text-left">Mata Pelajaran</th>
                <th width="80">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiAkademik as $n)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-left">{{ $n->nama_mapel }}</td>
                <td class="text-center"><strong>{{ $n->nilai_akhir ?? '-' }}</strong></td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center" style="color:#999;">Belum ada data nilai</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ====================== B & C — SEJAJAR (2 kolom) ====================== --}}
    <table style="border:none; width:100%;">
        <tr>
            <td style="border:none; width:60%; padding:0; padding-right:5px; vertical-align:top;">
                <div class="section">B. Ekstrakurikuler</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th width="35">No</th>
                            <th class="text-left">Kegiatan</th>
                            <th width="60">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiEkskul as $e)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $e->ekstrakurikuler->nama_ekskul ?? '-' }}</td>
                            <td class="text-center"><strong>{{ $e->nilai }}</strong></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center" style="color:#999;">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="border:none; width:40%; padding:0; padding-left:5px; vertical-align:top;">
                <div class="section">C. Ketidakhadiran</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th class="text-left">Keterangan</th>
                            <th width="80">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="text-left">Sakit</td><td class="text-center">{{ $kehadiran->sakit }} hari</td></tr>
                        <tr><td class="text-left">Izin</td><td class="text-center">{{ $kehadiran->izin }} hari</td></tr>
                        <tr><td class="text-left">Tanpa Keterangan</td><td class="text-center">{{ $kehadiran->alpa }} hari</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- ====================== TANDA TANGAN ====================== --}}
    <div class="ttd">
        <table>
            <tr>
                <td style="width:50%;">
                    Mengetahui,<br>
                    Orang Tua/Wali
                    <br><br><br><br>
                    <span style="border-bottom:1px solid #000; padding:0 60px;">&nbsp;</span>
                </td>
                <td style="width:50%; text-align:right;">
                    Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas
                    <br><br><br><br>
                    <span style="border-bottom:1px solid #000; padding:0 60px;">&nbsp;</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
