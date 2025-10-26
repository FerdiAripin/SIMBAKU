<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $filters['tipe'] == 'penggunaan' ? 'Laporan Cek Keluar' : 'Laporan Cek Masuk' }}</title>
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

        .summary-section {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 25px 0;
            font-size: 10pt;
        }

        .summary-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            text-align: center;
        }

        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .summary-row {
            display: table-row;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px 5px;
            border-right: 1px solid #ddd;
            width: 25%;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 9pt;
            color: #666;
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

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 15px;
            font-size: 10pt;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            padding: 3px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">BANK BJB KCP PEMKOT TASIKMALAYA</div>
        <div class="company-address">
            Jl. Ir. H. Juanda No.88, Panglayungan, Kec. Cipedes, Kab. Tasikmalaya, Jawa Barat 46151
        </div>
    </div>

    <div class="report-title" style="text-align: center;">
        {{ $filters['tipe'] == 'penggunaan' ? 'Laporan Cek Keluar' : 'Laporan Cek Masuk' }}
    </div>

    <div class="report-info">
        <table>
            <tr>
                <td class="label">Periode</td>
                <td class="colon">:</td>
                <td class="value">
                    @if ($filters['tanggal_dari'] && $filters['tanggal_sampai'])
                        {{ \Carbon\Carbon::parse($filters['tanggal_dari'])->translatedFormat('d F Y') }}
                        s/d
                        {{ \Carbon\Carbon::parse($filters['tanggal_sampai'])->translatedFormat('d F Y') }}
                    @else
                        Semua Periode
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Jenis Cek</td>
                <td class="colon">:</td>
                <td class="value">
                    @if ($filters['jenis_buku'])
                        Cek {{ $filters['jenis_buku'] }} Lembar
                    @else
                        Semua Jenis
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="colon">:</td>
                <td class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>

    @if ($filters['tipe'] == 'penggunaan')
        <table id="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Kode Buku</th>
                    <th style="width: 8%;">Jenis</th>
                    <th style="width: 31%;">Range Nomor Serial</th>
                    <th style="width: 29%;">Keperluan</th>
                    <th style="width: 12%;">Tgl Keluar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    @php
                        $lembarTerpakai = $item->lembarTerpakai()->where('status', 'terpakai');

                        if ($filters['tanggal_dari']) {
                            $lembarTerpakai->whereDate('tanggal_pakai', '>=', $filters['tanggal_dari']);
                        }
                        if ($filters['tanggal_sampai']) {
                            $lembarTerpakai->whereDate('tanggal_pakai', '<=', $filters['tanggal_sampai']);
                        }

                        $keperluan = $lembarTerpakai->pluck('keperluan')->filter()->unique()->values()->toArray();
                        $keperluanText = empty($keperluan)
                            ? '-'
                            : (count($keperluan) <= 2
                                ? implode(', ', $keperluan)
                                : implode(', ', array_slice($keperluan, 0, 2)) .
                                    ' +' .
                                    (count($keperluan) - 2) .
                                    ' lainnya');

                        $terpakai = $lembarTerpakai->pluck('nomor_seri')->sort()->values()->toArray();
                        $nomorSeriText = empty($terpakai)
                            ? '-'
                            : (count($terpakai) <= 3
                                ? implode(', ', $terpakai)
                                : $terpakai[0] . ' - ' . end($terpakai) . count($terpakai));

                        $tanggalMin = $lembarTerpakai->min('tanggal_pakai');
                        $tanggalMax = $lembarTerpakai->max('tanggal_pakai');
                        $tanggalText = !$tanggalMin
                            ? '-'
                            : ($tanggalMin == $tanggalMax
                                ? \Carbon\Carbon::parse($tanggalMin)->translatedFormat('d/m/Y')
                                : \Carbon\Carbon::parse($tanggalMin)->translatedFormat('d/m/Y') .
                                    ' s/d ' .
                                    \Carbon\Carbon::parse($tanggalMax)->translatedFormat('d/m/Y'));
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->buku_kode }}</td>
                        <td class="text-center">{{ $item->jenis_buku }}</td>
                        <td class="text-center">{{ $nomorSeriText }}</td>
                        <td class="text-center">{{ $keperluanText }}</td>
                        <td class="text-center">{{ $tanggalText }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table id="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">Kode Buku</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 30%;">Range Nomor Serial</th>
                    <th style="width: 25%;">Keterangan</th>
                    <th style="width: 10%;">Tgl Masuk</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->buku_kode }}</td>
                        <td class="text-center">{{ $item->jenis_buku }}</td>
                        <td class="text-center">{{ $item->nomor_seri_awal }} - {{ $item->nomor_seri_akhir }}</td>
                        <td class="text-center">{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            {{ $item->tanggal_terbit ? \Carbon\Carbon::parse($item->tanggal_terbit)->translatedFormat('d/m/Y') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
