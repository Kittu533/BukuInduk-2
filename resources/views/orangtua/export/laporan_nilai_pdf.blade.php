<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nilai - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 20px 25px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ============ KOP SURAT ============ */
        .kop {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none; padding: 0; vertical-align: middle; }
        .kop-logo-left { width: 90px; text-align: center; }
        .kop-logo-right { width: 90px; text-align: center; }
        .kop-logo-left img, .kop-logo-right img { width: 75px; height: auto; }
        .kop-text { text-align: center; }
        .kop-text .pemerintah { font-size: 13px; font-weight: bold; margin: 0; }
        .kop-text .dinas { font-size: 13px; font-weight: bold; margin: 0; }
        .kop-text .sekolah { font-size: 18px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .kop-text .alamat { font-size: 10px; margin: 1px 0; }
        .kop-text .kontak { font-size: 10px; margin: 1px 0; }

        /* ============ ISI ============ */
        h4.title {
            text-align: center;
            margin: 8px 0 12px;
            font-size: 13px;
            text-decoration: underline;
            text-transform: uppercase;
        }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        table.data th { background: #e8e8e8; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .section { font-weight: bold; margin: 8px 0 4px; font-size: 12px; }

        /* ============ INFO SISWA ============ */
        .info-table { width: 100%; margin-bottom: 8px; }
        .info-table td { border: none; padding: 2px 4px; font-size: 11px; }
        .info-table td.label { width: 90px; font-weight: bold; }
        .info-table td.sep { width: 10px; }

        /* ============ TTD ============ */
        .ttd { margin-top: 20px; font-size: 11px; }
        .ttd table { width: 100%; }
        .ttd table td { border: none; padding: 2px; vertical-align: top; }
    </style>
</head>
<body>

    {{-- ====================== KOP SURAT ====================== --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-left">
                    <img src="{{ public_path('img/logosma.png') }}" alt="Logo SMA">
                </td>
                <td class="kop-text">
                    <p class="pemerintah">PEMERINTAH PROVINSI JAWA TENGAH</p>
                    <p class="dinas">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                    <p class="sekolah">SMA NEGERI 3 CILACAP</p>
                    <p class="alamat">Jalan Kalimantan No. 14, Cilacap Tengah, Kabupaten Cilacap, Jawa Tengah 53224</p>
                    <p class="kontak">
                        Telp: (0282) 541809 | Email: info@sman3cilacap.sch.id | Website: www.sman3cilacap.sch.id
                    </p>
                </td>
                <td class="kop-logo-right">
                    <img src="{{ public_path('img/tutwuri.png') }}" alt="Tut Wuri Handayani">
                </td>
            </tr>
        </table>
    </div>

    <h4 class="title">Laporan Hasil Belajar Peserta Didik</h4>

    {{-- ====================== INFO SISWA ====================== --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nama_lengkap }}</td>

            <td class="label">Kelas</td>
            <td class="sep">:</td>
            <td>{{ $kelasData?->kelasAktif?->kelas?->nama_kelas ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">NIS</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nis }}</td>

            <td class="label">Semester</td>
            <td class="sep">:</td>
            <td>{{ $kelasData?->kelasAktif?->semester?->nama_semester ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">NISN</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nisn }}</td>

            <td class="label">Tahun Ajaran</td>
            <td class="sep">:</td>
            <td>{{ $kelasData?->kelasAktif?->tahunAjaran?->tahun ?? '-' }}</td>
        </tr>
    </table>

    {{-- ====================== A. NILAI WAJIB ====================== --}}
    <div class="section">A. Mata Pelajaran Kelompok Wajib</div>
    <table class="data">
        <thead>
            <tr>
                <th width="25">No</th>
                <th style="text-align:left;">Mata Pelajaran</th>
                <th width="35">T1</th>
                <th width="35">T2</th>
                <th width="35">T3</th>
                <th width="35">T4</th>
                <th width="35">T5</th>
                <th width="40">UTS</th>
                <th width="40">UAS</th>
                <th width="45">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiWajib as $n)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $n->nama_mapel }}</td>
                <td class="text-center">{{ $n->tugas1 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas2 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas3 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas4 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas5 ?? '-' }}</td>
                <td class="text-center">{{ $n->uts ?? '-' }}</td>
                <td class="text-center">{{ $n->uas ?? '-' }}</td>
                <td class="text-center"><strong>{{ $n->nilai_akhir ?? '-' }}</strong></td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ====================== B. NILAI PILIHAN ====================== --}}
    <div class="section">B. Mata Pelajaran Kelompok Pilihan</div>
    <table class="data">
        <thead>
            <tr>
                <th width="25">No</th>
                <th style="text-align:left;">Mata Pelajaran</th>
                <th width="35">T1</th>
                <th width="35">T2</th>
                <th width="35">T3</th>
                <th width="35">T4</th>
                <th width="35">T5</th>
                <th width="40">UTS</th>
                <th width="40">UAS</th>
                <th width="45">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiPilihan as $n)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $n->nama_mapel }}</td>
                <td class="text-center">{{ $n->tugas1 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas2 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas3 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas4 ?? '-' }}</td>
                <td class="text-center">{{ $n->tugas5 ?? '-' }}</td>
                <td class="text-center">{{ $n->uts ?? '-' }}</td>
                <td class="text-center">{{ $n->uas ?? '-' }}</td>
                <td class="text-center"><strong>{{ $n->nilai_akhir ?? '-' }}</strong></td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ====================== C & D — SEJAJAR ====================== --}}
    <table style="border:none; width:100%;">
        <tr>
            <td style="border:none; width:60%; padding:0; padding-right:5px; vertical-align:top;">
                <div class="section">C. Ekstrakurikuler</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th width="25">No</th>
                            <th style="text-align:left;">Kegiatan</th>
                            <th width="60">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiEkskul as $e)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $e->ekstrakurikuler->nama_ekskul ?? '-' }}</td>
                            <td class="text-center"><strong>{{ $e->nilai }}</strong></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="border:none; width:40%; padding:0; padding-left:5px; vertical-align:top;">
                <div class="section">D. Ketidakhadiran</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Keterangan</th>
                            <th width="80">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Sakit</td><td class="text-center">{{ $kehadiran->sakit }} hari</td></tr>
                        <tr><td>Izin</td><td class="text-center">{{ $kehadiran->izin }} hari</td></tr>
                        <tr><td>Tanpa Keterangan</td><td class="text-center">{{ $kehadiran->alpa }} hari</td></tr>
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
                    (......................................)
                </td>
                <td style="width:50%; text-align:right;">
                    Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas
                    <br><br><br><br>
                    (......................................)
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
