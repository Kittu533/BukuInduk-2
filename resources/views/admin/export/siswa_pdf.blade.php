<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Siswa - {{ $kelasAktif->kelas->nama_kelas }}</title>
    <style>
        @page { margin: 25px 30px; size: A4 portrait; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 10px;
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
        .kop-logo-left { width: 80px; text-align: center; }
        .kop-logo-right { width: 80px; text-align: center; }
        .kop-logo-left img, .kop-logo-right img { width: 65px; height: auto; }
        .kop-text { text-align: center; }
        .kop-text .pemerintah { font-size: 12px; font-weight: bold; margin: 0; }
        .kop-text .dinas { font-size: 12px; font-weight: bold; margin: 0; }
        .kop-text .sekolah { font-size: 16px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .kop-text .alamat { font-size: 10px; margin: 1px 0; }
        .kop-text .kontak { font-size: 10px; margin: 1px 0; }

        /* ============ ISI ============ */
        h4.title {
            text-align: center;
            margin: 8px 0 4px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        h5.subtitle {
            text-align: center;
            margin: 0 0 12px;
            font-size: 12px;
            font-weight: normal;
            font-style: italic;
        }

        /* ============ INFO KELAS ============ */
        .info-kelas {
            width: 100%;
            margin-bottom: 10px;
            font-size: 11px;
        }
        .info-kelas td {
            border: none;
            padding: 2px 4px;
        }
        .info-kelas td.label {
            width: 110px;
            font-weight: bold;
        }
        .info-kelas td.sep { width: 8px; }

        /* ============ TABLE DATA ============ */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
            font-size: 9px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        table.data thead th {
            background: #2c3e50;
            color: #fff;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        table.data tbody tr:nth-child(even) {
            background: #f5f7fa;
        }
        table.data tbody td.text-center { text-align: center; }
        table.data tbody td.fw-bold { font-weight: bold; }
        .badge-l { background: #dbeafe; color: #1e40af; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-p { background: #fce7f3; color: #be185d; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }

        /* ============ FOOTER / TTD ============ */
        .footer-info {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #555;
        }
        .ttd { margin-top: 25px; font-size: 11px; }
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

    {{-- ====================== JUDUL ====================== --}}
    <h4 class="title">Daftar Peserta Didik</h4>
    <h5 class="subtitle">Tahun Pelajaran {{ $kelasAktif->tahunAjaran->tahun ?? '-' }} — Semester {{ $kelasAktif->semester->nama_semester ?? '-' }}</h5>

    {{-- ====================== INFO KELAS ====================== --}}
    <table class="info-kelas">
        <tr>
            <td class="label">Kelas</td>
            <td class="sep">:</td>
            <td><strong>{{ $kelasAktif->kelas->nama_kelas ?? '-' }}</strong></td>
            <td class="label">Wali Kelas</td>
            <td class="sep">:</td>
            <td><strong>{{ $kelasAktif->guru->nama_guru ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Jumlah Siswa</td>
            <td class="sep">:</td>
            <td>{{ $siswa->count() }} orang</td>
            <td class="label">Tanggal Cetak</td>
            <td class="sep">:</td>
            <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    {{-- ====================== TABEL DATA ====================== --}}
    <table class="data">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="50">NIS</th>
                <th width="65">NISN</th>
                <th>Nama Lengkap</th>
                <th width="30">L/P</th>
                <th width="70">Tempat Lahir</th>
                <th width="65">Tgl Lahir</th>
                <th width="55">Agama</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $s)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $s->nis }}</td>
                <td class="text-center">{{ $s->nisn }}</td>
                <td class="fw-bold">{{ strtoupper($s->nama_lengkap) }}</td>
                <td class="text-center">
                    @if(strtolower(substr($s->jenis_kelamin, 0, 1)) === 'l')
                        <span class="badge-l">L</span>
                    @else
                        <span class="badge-p">P</span>
                    @endif
                </td>
                <td>{{ $s->tempat_lahir }}</td>
                <td class="text-center">
                    {{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d-m-Y') : '-' }}
                </td>
                <td>{{ $s->agama ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding:20px; color:#999;">Belum ada data siswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ====================== FOOTER INFO ====================== --}}
    <div class="footer-info">
        <em>Dokumen ini dicetak dari Sistem Buku Induk Siswa SMA Negeri 3 Cilacap</em>
    </div>

    {{-- ====================== TANDA TANGAN ====================== --}}
    <div class="ttd">
        <table>
            <tr>
                <td style="width:50%;">
                    Mengetahui,<br>
                    Kepala Sekolah
                    <br><br><br><br>
                    <strong>{{ $profil->kepala_sekolah ?? '...........................' }}</strong><br>
                    NIP. {{ $profil->nip_kepala_sekolah ?? '...............' }}
                </td>
                <td style="width:50%; text-align:right;">
                    Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas
                    <br><br><br><br>
                    <strong>{{ $kelasAktif->guru->nama_guru ?? '...........................' }}</strong><br>
                    NIP. {{ $kelasAktif->guru->nip ?? '...............' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
