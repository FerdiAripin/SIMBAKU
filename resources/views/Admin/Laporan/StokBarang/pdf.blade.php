<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $web->web_deskripsi }}">
    <meta name="author" content="{{ $web->web_nama }}">
    <meta name="keywords" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $title }}</title>

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
            margin-top: 20px;
            font-size: 9pt;
        }

        #main-table th {
            border: 1px solid #000;
            padding: 8px 4px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
        }

        #main-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
            font-size: 9pt;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            font-style: italic;
            font-size: 12pt;
        }

        .summary {
            margin-top: 20px;
            font-size: 10pt;
        }

        .summary table {
            border-collapse: collapse;
        }

        .summary td {
            padding: 3px 0;
        }

        .summary .label {
            width: 200px;
            font-weight: bold;
        }

        .summary .colon {
            width: 20px;
            text-align: center;
        }

        .col-no {
            width: 4%;
        }

        .col-code {
            width: 12%;
        }

        .col-name {
            width: 25%;
        }

        .col-category {
            width: 15%;
        }

        .col-stock {
            width: 11%;
        }

        .col-in {
            width: 11%;
        }

        .col-out {
            width: 11%;
        }

        .col-total {
            width: 11%;
        }
    </style>

    <?php
    use App\Models\Admin\BarangkeluarModel;
    use App\Models\Admin\BarangmasukModel;
    use Carbon\Carbon;
    ?>
</head>

<body>
    <div class="header">
        <div class="company-name">BANK BJB KCP PEMKOT TASIKMALAYA</div>
        <div class="company-address">
            {{ $web->web_alamat ?? 'Jl. Ir. H. Juanda No.88, Panglayungan, Kec. Cipedes, Kab. Tasikmalaya, Jawa Barat 46151' }}
        </div>
    </div>

    <div class="report-title" style="text-align: center; align-items: center">Data Stok</div>

    <div class="report-info">
        <table>
            <tr>
                <td class="label">Jenis Produk</td>
                <td class="colon">:</td>
                <td class="value jenis-produk">
                    @if($jenis == 'baru')
                        Produk Baru
                    @else
                        Produk Lama
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

            @if ($kategori_nama)
                <tr>
                    <td class="label">Kategori</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $kategori_nama }}</td>
                </tr>
            @endif

            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="colon">:</td>
                <td class="value">{{ Carbon::now()->translatedFormat('d F Y')}}</td>
            </tr>
        </table>
    </div>


    @if (isset($data) && count($data) > 0)
        <table id="main-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-code">Kode Produk</th>
                    <th class="col-name">Produk</th>
                    <th class="col-category">Kategori</th>
                    <th class="col-stock">Stok Awal</th>
                    <th class="col-in">Jml Masuk</th>
                    <th class="col-out">Jml Keluar</th>
                    <th class="col-total">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $totalStokAwal = 0;
                    $totalMasuk = 0;
                    $totalKeluar = 0;
                    $totalStokAkhir = 0;
                @endphp
                @foreach ($data as $d)
                    <?php
                    if ($tglawal == '') {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->where('tbl_barangmasuk.barang_kode', '=', $d->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                            ->where('tbl_barangmasuk.barang_kode', '=', $d->barang_kode)
                            ->whereBetween('bm_tanggal', [$tglawal, $tglakhir])
                            ->sum('tbl_barangmasuk.bm_jumlah');
                    }

                    if ($tglawal != '') {
                        $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
                            ->where('tbl_barangkeluar.barang_kode', '=', $d->barang_kode)
                            ->sum('tbl_barangkeluar.bk_jumlah');
                    } else {
                        $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->where('tbl_barangkeluar.barang_kode', '=', $d->barang_kode)->sum('tbl_barangkeluar.bk_jumlah');
                    }

                    $totalStok = $d->barang_stok + ($jmlmasuk - $jmlkeluar);
                    $totalStokAwal += $d->barang_stok;
                    $totalMasuk += $jmlmasuk;
                    $totalKeluar += $jmlkeluar;
                    $totalStokAkhir += $totalStok;
                    ?>
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ $d->barang_kode }}</td>
                        <td class="text-center">{{ $d->barang_nama }}</td>
                        <td class="text-center">{{ $d->kategori_nama ?? '-' }}</td>
                        <td class="text-center">{{ number_format($d->barang_stok, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($jmlmasuk, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($jmlkeluar, 0, ',', '.') }}</td>
                        <td class="text-center font-bold">{{ number_format($totalStok, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <!-- Total Row -->
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td class="text-center" colspan="4">TOTAL</td>
                    <td class="text-center">{{ number_format($totalStokAwal, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($totalStokAkhir, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>TIDAK ADA DATA</h3>
            <p>Tidak ada data stok produk untuk filter yang dipilih</p>
        </div>
    @endif

</body>
</html>
