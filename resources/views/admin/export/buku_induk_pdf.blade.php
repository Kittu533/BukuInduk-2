<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Induk Siswa - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 30px 35px; size: A4 portrait; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        h2.title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 12px;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
        }

        .info-header {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }
        .info-header > div {
            display: table-cell;
            font-weight: bold;
            font-size: 11px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin: 10px 0 4px;
            text-align:left;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .data-table td {
            border: none;
            padding: 2px 4px;
            font-size: 11px;
            vertical-align: top;
        }
        .data-table td.no { width: 25px; text-align: left; }
        .data-table td.label { width: 145px; }
        .data-table td.sublabel { width: 145px; padding-left: 28px; }
        .data-table td.sep { width: 12px; }

        .layout {
            display: table;
            width: 100%;
        }
        .layout-left {
            display: table-cell;
            width: 75%;
            vertical-align: top;
        }
        .layout-right {
            display: table-cell;
            width: 25%;
            vertical-align: top;
            padding-left: 12px;
        }
        .photo-box {
            width: 130px;
            height: 170px;
            border: 1px solid #999;
            background: #d0d0d0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto;
        }
        .photo-box .silhouette {
            font-size: 80px;
            color: #999;
            text-align: center;
        }

        .ttd {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            margin-left:420px;
            margin-top:50px;
        }
        .ttd .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .label{
            width:190px;
        }

        .sub-label{
            width:190px;
            padding-left:25px;
        }

        .titik{
            width:15px;
            text-align:center;
        }
    </style>
</head>
<body>

    {{-- ====================== JUDUL ====================== --}}
    <h2 class="title">BUKU INDUK SISWA SMA NEGERI 3 CILACAP</h2>

    {{-- ====================== HEADER INFO ====================== --}}
    <div class="info-header">
        <div>Nomor Induk : {{ $siswa->nis }}</div>
        <div>NISN : {{ $siswa->nisn }}</div>
        <div>Thn Masuk : {{ $siswa->tahun_masuk ? \Carbon\Carbon::parse($siswa->tahun_masuk)->translatedFormat('d F Y') : '-' }}</div>
    </div>

    {{-- ====================== LAYOUT 2 KOLOM ====================== --}}
    <div class="layout">

        {{-- ============= KIRI: DATA ============= --}}
        <div class="layout-left">

            {{-- A. KETERANGAN SISWA --}}
            <div class="section-title">A. KETERANGAN SISWA</div>
            <table class="data-table">
                <tr>
                    <td class="no">1.</td>
                    <td class="label">Nama Siswa</td>
                    <td class="sep">:</td>
                    <td>{{ strtoupper($siswa->nama_lengkap) }}</td>
                </tr>
                <tr>
                    <td class="no">2.</td>
                    <td class="label">Jenis Kelamin</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td class="no">3.</td>
                    <td class="label">Kelahiran</td>
                    <td class="sep"></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="sublabel">a. Tempat</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->tempat_lahir ?? '-' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="sublabel">b. Tanggal</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="no">4.</td>
                    <td class="label">Agama</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->agama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="no">5.</td>
                    <td class="label">Status dalam Keluarga</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->status_keluarga ?? '' }}</td>
                </tr>
                <tr>
                    <td class="no">6.</td>
                    <td class="label">Anak ke</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->anak_ke ?? '' }}</td>
                </tr>
                <tr>
                    <td class="no">7.</td>
                    <td class="label">Alamat</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->alamat ?? '' }}</td>
                </tr>
                <tr>
                    <td class="no">8.</td>
                    <td class="label">Nomor Telepon</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->no_telp ?? '' }}</td>
                </tr>
            </table>

        </div>

        {{-- ============= KANAN: FOTO ============= --}}
        <div class="layout-right">
            <div class="photo-box">
                <div class="silhouette">👤</div>
            </div>
        </div>

    </div>

    {{-- B. KETERANGAN ORANG TUA / WALI --}}
    <div class="section-title">B. KETERANGAN ORANG TUA / WALI SISWA</div>
    <table class="data-table">
        <tr>
            <td class="label" colspan="2">Nama Orangtua</td>
            <td class="sep"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">a. Ayah</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nama_ayah ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">b. Ibu</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nama_ibu ?? '' }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Pekerjaan Orangtua</td>
            <td class="sep"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">a. Ayah</td>
            <td class="sep">:</td>
            <td>{{ $siswa->pekerjaan_ayah ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">b. Ibu</td>
            <td class="sep">:</td>
            <td>{{ $siswa->pekerjaan_ibu ?? '' }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Alamat Orangtua</td>
            <td class="sep">:</td>
            <td>{{ $siswa->alamat_ortu ?? '' }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Telepon Orangtua</td>
            <td class="sep">:</td>
            <td>{{ $siswa->no_telp_ortu ?? '' }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Wali Siswa</td>
            <td class="sep"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">a. Nama</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nama_wali ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">b. Pekerjaan</td>
            <td class="sep">:</td>
            <td>{{ $siswa->pekerjaan_wali ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">c. Alamat</td>
            <td class="sep">:</td>
            <td>{{ $siswa->alamat_wali ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">d. Telepon</td>
            <td class="sep">:</td>
            <td>{{ $siswa->no_telp_wali ?? '' }}</td>
        </tr>
    </table>

    {{-- C. PERKEMBANGAN SISWA --}}
    <div class="section-title">C. PERKEMBANGAN SISWA</div>
    <table class="data-table">
        <tr>
            <td class="label" colspan="2">Sekolah Asal</td>
            <td class="sep">:</td>
            <td>{{ strtoupper($siswa->asal_sekolah ?? '-') }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Diterima di sekolah ini</td>
            <td class="sep"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">a. Dikelas</td>
            <td class="sep">:</td>
            <td>{{ $kelasPertama ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="sublabel">b. Pada Tanggal</td>
            <td class="sep">:</td>
            <td>{{ $siswa->tahun_masuk ? \Carbon\Carbon::parse($siswa->tahun_masuk)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    {{-- TTD --}}
    <div class="ttd">
        <p style="margin:0;">Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p style="margin:0;">Kepala Sekolah</p>
        <br><br><br>
        <p class="name" style="margin:0;">{{ $profil->kepala_sekolah ?? 'Sumarsono' }}</p>
        <p style="margin:0; font-weight:bold;">NIP. {{ $profil->nip_kepala_sekolah ?? '196707121994121006' }}</p>
    </div>

</body>
</html>
