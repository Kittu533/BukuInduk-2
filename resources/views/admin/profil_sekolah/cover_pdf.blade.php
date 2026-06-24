<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cover Buku Induk</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .cover {
            padding: 70px 60px 50px;
            text-align: center;
        }
        .logo-wrap { margin-top: 20px; margin-bottom: 30px; }
        .logo-wrap img { width: 150px; height: auto; }
        .title-main {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }
        .title-sub {
            font-size: 22px;
            font-weight: bold;
            margin: 8px 0 4px;
        }
        .title-sub-2 {
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 50px;
        }
        .info-block { margin: 25px 0; }
        .info-label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .info-value {
            display: inline-block;
            border: 1px solid #000;
            padding: 10px 30px;
            font-size: 16px;
            font-weight: bold;
            min-width: 280px;
            margin-top: 4px;
        }
        .footer-text {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.6;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <div class="cover">

        <div class="logo-wrap">
            <img src="{{ public_path('img/tutwuri.png') }}" alt="Tut Wuri Handayani">
        </div>

        <h1 class="title-main">BUKU INDUK SISWA</h1>
        <h2 class="title-sub">SEKOLAH MENENGAH ATAS</h2>
        <h2 class="title-sub-2">( SMA )</h2>

        <div class="info-block">
            <div class="info-label">NAMA SEKOLAH :</div>
            <div class="info-value">{{ strtoupper($namaSekolah ?? 'SMA NEGERI 3 CILACAP') }}</div>
        </div>

        @if(!empty($namaKelas))
        <div class="info-block">
            <div class="info-label">KELAS :</div>
            <div class="info-value">{{ $namaKelas }}</div>
        </div>
        @endif

        @if(!empty($namaSiswa))
        <div class="info-block">
            <div class="info-label">NAMA SISWA :</div>
            <div class="info-value">{{ $namaSiswa }}</div>
        </div>
        @endif

        @if(!empty($nis))
        <div class="info-block">
            <div class="info-label">NIS :</div>
            <div class="info-value">{{ $nis }}</div>
        </div>
        @endif

        <div class="footer-text">
            KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br>
            RISET DAN TEKNOLOGI<br>
            REPUBLIK INDONESIA
        </div>

    </div>

</body>
</html>
