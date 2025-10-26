<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $web->web_deskripsi }}">
    <meta name="author" content="{{ $web->web_nama }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Laporan Produk Masuk</title>

    <style>
        @page {
            size: 8.5in 13in;
            margin: 1in 0.75in;
        }

        * {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-size: 11pt;
            line-height: 1.2;
            color: #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-top: 10px;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .company-address {
            font-size: 9pt;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .report-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .report-info {
            margin: 25px 0;
            font-size: 10pt;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-info td {
            padding: 3px 0;
            vertical-align: top;
        }

        .report-info .label {
            width: 15%;
            font-weight: bold;
        }

        .report-info .colon {
            width: 2%;
            text-align: center;
        }

        .report-info .value {
            width: 83%;
        }

        #main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 9pt;
        }

        #main-table th {
            border: 1px solid #000;
            padding: 10px 6px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
        }

        #main-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            vertical-align: middle;
            font-size: 9pt;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            font-style: italic;
            font-size: 12pt;
        }

        .summary {
            margin-top: 25px;
            /* Increased margin */
            font-size: 10pt;
            padding: 10px 0;
            /* Added padding */
        }

        .summary table {
            border-collapse: collapse;
        }

        .summary td {
            padding: 3px 0;
        }

        .summary .label {
            width: 150px;
            font-weight: bold;
        }

        .summary .colon {
            width: 20px;
            text-align: center;
        }
    </style>

    <?php
    use Carbon\Carbon;
    ?>

</head>

<body>
    <div class="header">
        <div class="company-name">BANK BJB KCP PEMKOT TASIKMALAYA</div>
        <div class="company-address">
            Jl. Ir. H. Juanda No.88, Panglayungan, Kec. Cipedes, Kab. Tasikmalaya, Jawa Barat 46151
        </div>
    </div>

    <div class="report-title" style="text-align: center; align-items: center">Laporan Produk Masuk</div>

    <div class="report-info">
        <table>
            <tr>
                <td class="label">Jenis Produk</td>
                <td class="colon">:</td>
                <td class="value jenis-produk">
                    @if (isset($jenis) && $jenis == 'baru')
                        Produk Baru
                    @elseif(isset($jenis) && $jenis == 'lama')
                        Produk Lama
                    @else
                        Semua
                    @endif
                </td>
            </tr>

            @if ($tglawal != '')
                <tr>
                    <td class="label">Periode</td>
                    <td class="colon">:</td>
                    <td class="value">{{ Carbon::parse($tglawal)->translatedFormat('d F Y') }} s/d
                        {{ Carbon::parse($tglakhir)->translatedFormat('d F Y') }}</td>
                </tr>
            @else
                <tr>
                    <td class="label">Periode</td>
                    <td class="colon">:</td>
                    <td class="value">Semua Tanggal</td>
                </tr>
            @endif

            @if (isset($kategori_nama) && $kategori_nama)
                <tr>
                    <td class="label">Kategori</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $kategori_nama }}</td>
                </tr>
            @endif

            <tr>
                <td class="label">Dicetak</td>
                <td class="colon">:</td>
                <td class="value">{{ date('d F Y') }}</td>
            </tr>
        </table>
    </div>

    @if (isset($data) && count($data) > 0)
        <table id="main-table">
            <thead>
                <tr>
                    <th style="width: 3%;">NO</th>
                    <th style="width: 10%;">TGL MASUK</th>
                    <th style="width: 17%;">KODE BM</th>
                    <th style="width: 16%;">KODE PRODUK</th>
                    <th style="width: 20%;">NAMA PRODUK</th>
                    <th style="width: 12%;">KATEGORI</th>
                    <th style="width: 8%;">JUMLAH</th>
                    <th style="width: 14%;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $totalJumlah = 0;
                @endphp
                @foreach ($data as $row)
                    @php
                        $totalJumlah += isset($row->bm_jumlah) ? $row->bm_jumlah : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">
                            @if (isset($row->bm_tanggal) && $row->bm_tanggal)
                                {{ date('d/m/Y', strtotime($row->bm_tanggal)) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ isset($row->bm_kode) ? $row->bm_kode : '-' }}</td>
                        <td class="text-center">{{ isset($row->barang_kode) ? $row->barang_kode : '-' }}</td>
                        <td class="text-center">{{ isset($row->barang_nama) ? $row->barang_nama : '-' }}</td>
                        <td class="text-center">{{ isset($row->kategori_nama) ? $row->kategori_nama : '-' }}</td>
                        <td class="text-center">
                            {{ isset($row->bm_jumlah) ? number_format($row->bm_jumlah, 0, ',', '.') : '0' }}</td>
                        <td class="text-center">{{ isset($row->bm_keterangan) ? $row->bm_keterangan : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <td class="label">Jumlah Data</td>
                    <td class="colon">:</td>
                    <td>{{ count($data) }} data</td>
                </tr>
                <tr>
                    <td class="label">Total Produk Masuk</td>
                    <td class="colon">:</td>
                    <td><strong>{{ number_format($totalJumlah, 0, ',', '.') }} unit</strong></td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            <h3>TIDAK ADA DATA</h3>
            <p>Tidak ada data produk masuk untuk filter yang dipilih</p>
        </div>
    @endif
</body>

</html>
