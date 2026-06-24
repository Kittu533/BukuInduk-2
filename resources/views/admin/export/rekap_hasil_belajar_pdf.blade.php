<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

@page{
    margin:15px;
}

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:11px;
    color:#000;
}

.title{
    text-align:center;
    font-size:20px;
    font-weight:bold;
    margin-bottom:15px;
}

.section-title{
    font-size:14px;
    font-weight:bold;
    margin-top:12px;
    margin-bottom:5px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:12px;
    page-break-inside:auto;
}

table,
th,
td{
    border:1px solid #000;
}

th{
    background:#d9d9d9;
    text-align:center;
    font-weight:bold;
}

td,
th{
    padding:4px 6px;
    vertical-align:middle;
}

tr{
    page-break-inside:avoid;
}

.center{
    text-align:center;
}

.left{
    text-align:left;
}

.group{
    background:#ececec;
    font-weight:bold;
}

.ttd{
    width:250px;
    float:right;
    text-align:center;
    margin-top:20px;
}

.nama-ttd{
    font-weight:bold;
    text-decoration:underline;
}

.kenaikan td{
    height:35px;
    vertical-align:middle;
    font-weight:bold;
}

</style>

</head>
<body>

<div class="title">
    BUKU INDUK SISWA
</div>

<table>

    <tr>

        <td colspan="2">
            <b>NAMA :</b>
            {{ strtoupper($siswa->nama_lengkap) }}
        </td>

        <td colspan="2" class="center">
            {{ $tahunHeaders[0] ?? 'X' }}
        </td>

        <td colspan="2" class="center">
            {{ $tahunHeaders[1] ?? 'XI' }}
        </td>

        <td colspan="2" class="center">
            {{ $tahunHeaders[2] ?? 'XII' }}
        </td>

    </tr>

    <tr>

        <td colspan="2">
            <b>NISN :</b>
            {{ $siswa->nisn ?? '-' }}
        </td>

        <td colspan="2" class="center">
            SEMESTER
        </td>

        <td colspan="2" class="center">
            SEMESTER
        </td>

        <td colspan="2" class="center">
            SEMESTER
        </td>

    </tr>

    <tr>

        <td colspan="2">
            <b>NIS :</b>
            {{ $siswa->nis }}
        </td>

        <td class="center">GANJIL</td>
        <td class="center">GENAP</td>

        <td class="center">GANJIL</td>
        <td class="center">GENAP</td>

        <td class="center">GANJIL</td>
        <td class="center">GENAP</td>

    </tr>

    <tr>

        <th width="35">NO</th>
        <th>MATA PELAJARAN</th>

        <th width="50">NILAI</th>
        <th width="50">NILAI</th>

        <th width="50">NILAI</th>
        <th width="50">NILAI</th>

        <th width="50">NILAI</th>
        <th width="50">NILAI</th>

    </tr>

    <tr>
        <td colspan="8" class="group">
            Kelompok Umum
        </td>
    </tr>

    @foreach($mapelWajib as $index => $mapel)

    <tr>

        <td class="center">
            {{ $index + 1 }}
        </td>

        <td>
            {{ $mapel['nama_mapel'] }}
        </td>

        @foreach($mapel['nilai'] as $nilai)

            <td class="center">
                {{ $nilai }}
            </td>

        @endforeach

    </tr>

    @endforeach

    <tr>
        <td colspan="8" class="group">
            Kelompok Mapel Pilihan
        </td>
    </tr>

    @foreach($mapelPilihan as $index => $mapel)

    <tr>

        <td class="center">
            {{ count($mapelWajib) + $index + 1 }}
        </td>

        <td>
            {{ $mapel['nama_mapel'] }}
        </td>

        @foreach($mapel['nilai'] as $nilai)

            <td class="center">
                {{ $nilai }}
            </td>

        @endforeach

    </tr>

    @endforeach

</table>

<div class="section-title">
    EKSTRAKURIKULER
</div>

<table>

    <tr>

        <th width="35">No</th>
        <th>Nama Ekstrakurikuler</th>

        <th width="50">X-1</th>
        <th width="50">X-2</th>

        <th width="50">XI-1</th>
        <th width="50">XI-2</th>

        <th width="50">XII-1</th>
        <th width="50">XII-2</th>

    </tr>

    @forelse($daftarEkskul as $i => $ekskul)

    <tr>

        <td class="center">
            {{ $i + 1 }}
        </td>

        <td>
            {{ $ekskul->nama_ekskul }}
        </td>

        @for($s=0; $s<6; $s++)

            <td class="center">
                {{ $nilaiEkskulPerSemester[$ekskul->id_ekskul][$s] ?? '-' }}
            </td>

        @endfor

    </tr>

    @empty

    <tr>

        <td class="center">1</td>
        <td>-</td>

        <td class="center">-</td>
        <td class="center">-</td>

        <td class="center">-</td>
        <td class="center">-</td>

        <td class="center">-</td>
        <td class="center">-</td>

    </tr>

    @endforelse

</table>

<div class="section-title">
    KETIDAKHADIRAN
</div>

<table>

    <tr>

        <th width="35">No</th>
        <th>Keterangan</th>

        <th width="50">X-1</th>
        <th width="50">X-2</th>

        <th width="50">XI-1</th>
        <th width="50">XI-2</th>

        <th width="50">XII-1</th>
        <th width="50">XII-2</th>

    </tr>

    <tr>

        <td class="center">1</td>
        <td>Sakit</td>

        @for($i=0;$i<6;$i++)

            <td class="center">
                {{ $kehadiranPerSemester[$i]['sakit'] ?? '-' }}
            </td>

        @endfor

    </tr>

    <tr>

        <td class="center">2</td>
        <td>Izin</td>

        @for($i=0;$i<6;$i++)

            <td class="center">
                {{ $kehadiranPerSemester[$i]['izin'] ?? '-' }}
            </td>

        @endfor

    </tr>

    <tr>

        <td class="center">3</td>
        <td>Tanpa Keterangan</td>

        @for($i=0;$i<6;$i++)

            <td class="center">
                {{ $kehadiranPerSemester[$i]['alpa'] ?? '-' }}
            </td>

        @endfor

    </tr>

</table>

<table class="kenaikan">

    <tr>

        <td width="35%">
            KENAIKAN / KELULUSAN
        </td>

        <td width="21%" class="center">
            {{ $kenaikan[0] ?? '-' }}
        </td>

        <td width="22%" class="center">
            {{ $kenaikan[1] ?? '-' }}
        </td>

        <td width="22%" class="center">
            {{ $kenaikan[2] ?? '-' }}
        </td>

    </tr>

</table>

<div class="ttd">

    <div>
        Cilacap,
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>

    <div>
        Kepala Sekolah
    </div>

    <br><br><br><br>

    <div class="nama-ttd">
        {{ $profil->kepala_sekolah ?? 'Sumarsono' }}
    </div>

    <div>
        NIP.
        {{ $profil->nip_kepala_sekolah ?? '196707121994121006' }}
    </div>

</div>

</body>
</html>