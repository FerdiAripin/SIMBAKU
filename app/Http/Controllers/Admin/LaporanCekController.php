<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\BukuCek;
use App\Models\StokCek;
use App\Models\LembarCek;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanCekController extends Controller
{
    public function index()
    {
        $title = "Laporan Cek Lengkap";
        return view('Admin.LaporanCek.index', compact('title'));
    }

    public function getLaporanPenggunaan(Request $request)
    {
        if ($request->ajax()) {
            $query = BukuCek::with(['lembarTerpakai'])
                ->whereHas('lembarTerpakai', function ($q) use ($request) {
                    $q->where('status', 'terpakai');

                    if ($request->filled('tanggal_dari')) {
                        $q->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                    }
                    if ($request->filled('tanggal_sampai')) {
                        $q->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                    }
                })
                ->orderBy('created_at', 'desc');

            if ($request->filled('jenis_buku')) {
                $query->where('jenis_buku', $request->jenis_buku);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('buku_kode', function ($row) {
                    return $row->buku_kode;
                })
                ->addColumn('jenis_buku', function ($row) {
                    return "Cek {$row->jenis_buku} Lembar";
                })
                ->addColumn('nomor_seri', function ($row) use ($request) {
                    $lembarTerpakai = $row->lembarTerpakai()->where('status', 'terpakai');

                    if ($request->filled('tanggal_dari')) {
                        $lembarTerpakai->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                    }
                    if ($request->filled('tanggal_sampai')) {
                        $lembarTerpakai->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                    }

                    $terpakai = $lembarTerpakai->pluck('nomor_seri')->sort()->values()->toArray();

                    if (empty($terpakai)) {
                        return '-';
                    }
                    if (count($terpakai) <= 3) {
                        return implode(', ', $terpakai);
                    }

                    return $terpakai[0] . ' - ' . end($terpakai);
                })
                ->addColumn('keperluan_list', function ($row) use ($request) {
                    $lembarTerpakai = $row->lembarTerpakai()->where('status', 'terpakai');

                    if ($request->filled('tanggal_dari')) {
                        $lembarTerpakai->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                    }
                    if ($request->filled('tanggal_sampai')) {
                        $lembarTerpakai->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                    }

                    $keperluan = $lembarTerpakai->pluck('keperluan')->filter()->unique()->values()->toArray();

                    if (empty($keperluan)) {
                        return '-';
                    }

                    if (count($keperluan) <= 2) {
                        return implode(', ', $keperluan);
                    }

                    return implode(', ', array_slice($keperluan, 0, 2)) . ' +' . (count($keperluan) - 2) . ' lainnya';
                })
                ->addColumn('tanggal_range', function ($row) use ($request) {
                    $lembarTerpakai = $row->lembarTerpakai()->where('status', 'terpakai');

                    if ($request->filled('tanggal_dari')) {
                        $lembarTerpakai->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                    }
                    if ($request->filled('tanggal_sampai')) {
                        $lembarTerpakai->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                    }

                    $tanggalMin = $lembarTerpakai->min('tanggal_pakai');
                    $tanggalMax = $lembarTerpakai->max('tanggal_pakai');

                    if (!$tanggalMin) return '-';

                    if ($tanggalMin == $tanggalMax) {
                        return Carbon::parse($tanggalMin)->translatedFormat('d/m/Y');
                    }

                    return Carbon::parse($tanggalMin)->translatedFormat('d/m/Y') . ' s/d ' . Carbon::parse($tanggalMax)->translatedFormat('d/m/Y');
                })
                ->make(true);
        }
    }

    /**
     * Data untuk laporan masuk cek (penambahan stok)
     */
    public function getLaporanMasuk(Request $request)
    {
        if ($request->ajax()) {
            $query = BukuCek::with(['lembarTersedia', 'lembarTerpakai'])
                ->orderBy('created_at', 'desc');

            // Filter tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('created_at', '>=', $request->tanggal_dari);
            }

            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('created_at', '<=', $request->tanggal_sampai);
            }

            // Filter jenis buku
            if ($request->filled('jenis_buku')) {
                $query->where('jenis_buku', $request->jenis_buku);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('buku_kode', function ($row) {
                    return $row->buku_kode;
                })
                ->addColumn('jenis_display', function ($row) {
                    return "Cek {$row->jenis_buku} Lembar";
                })
                ->addColumn('nomor_seri_range', function ($row) {
                    return $row->nomor_seri_awal . ' - ' . $row->nomor_seri_akhir;
                })
                ->addColumn('keterangan', function ($row) {
                    return $row->keterangan ?? '-';
                })
                ->addColumn('tanggal_terbit', function ($row) {
                    return $row->tanggal_terbit ?
                        Carbon::parse($row->tanggal_terbit)->translatedFormat('d/m/Y') : '-';
                })
                ->make(true);
        }
    }

    /**
     * Data untuk laporan gabungan semua tipe
     */
    public function getLaporanSemua(Request $request)
    {
        if ($request->ajax()) {
            $dataPenggunaan = collect();
            $dataMasuk = collect();
            $queryPenggunaan = BukuCek::with(['lembarTerpakai'])
                ->whereHas('lembarTerpakai', function ($q) use ($request) {
                    $q->where('status', 'terpakai');

                    if ($request->filled('tanggal_dari')) {
                        $q->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                    }
                    if ($request->filled('tanggal_sampai')) {
                        $q->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                    }
                });

            if ($request->filled('jenis_buku')) {
                $queryPenggunaan->where('jenis_buku', $request->jenis_buku);
            }

            $bukuPenggunaan = $queryPenggunaan->get();

            foreach ($bukuPenggunaan as $buku) {
                $lembarTerpakai = $buku->lembarTerpakai()->where('status', 'terpakai');

                if ($request->filled('tanggal_dari')) {
                    $lembarTerpakai->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                }
                if ($request->filled('tanggal_sampai')) {
                    $lembarTerpakai->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                }

                $terpakai = $lembarTerpakai->pluck('nomor_seri')->sort()->values()->toArray();
                $keperluan = $lembarTerpakai->pluck('keperluan')->filter()->unique()->values()->toArray();
                $tanggalMin = $lembarTerpakai->min('tanggal_pakai');
                $tanggalMax = $lembarTerpakai->max('tanggal_pakai');

                $nomorSeriText = empty($terpakai) ? '-' : (count($terpakai) <= 3 ? implode(', ', $terpakai) : $terpakai[0] . ' - ' . end($terpakai) . ' (' . count($terpakai) . ' lembar)');
                $keperluanText = empty($keperluan) ? '-' : (count($keperluan) <= 2 ? implode(', ', $keperluan) : implode(', ', array_slice($keperluan, 0, 2)) . ' +' . (count($keperluan) - 2) . ' lainnya');
                $tanggalText = !$tanggalMin ? '-' : ($tanggalMin == $tanggalMax ? Carbon::parse($tanggalMin)->translatedFormat('d/m/Y') : Carbon::parse($tanggalMin)->translatedFormat('d/m/Y') . ' s/d ' . Carbon::parse($tanggalMax)->translatedFormat('d/m/Y'));

                $dataPenggunaan->push([
                    'buku_kode' => $buku->buku_kode,
                    'tipe_display' => 'Cek Keluar',
                    'jenis_display' => "Cek {$buku->jenis_buku} Lembar",
                    'nomor_seri_range' => $nomorSeriText,
                    'info_mixed' => $keperluanText,
                    'tanggal_mixed' => $tanggalText,
                    'sort_date' => $tanggalMin
                ]);
            }

            // Data masuk
            $queryMasuk = BukuCek::orderBy('created_at', 'desc');

            if ($request->filled('tanggal_dari')) {
                $queryMasuk->whereDate('created_at', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $queryMasuk->whereDate('created_at', '<=', $request->tanggal_sampai);
            }
            if ($request->filled('jenis_buku')) {
                $queryMasuk->where('jenis_buku', $request->jenis_buku);
            }

            $bukuMasuk = $queryMasuk->get();

            foreach ($bukuMasuk as $buku) {
                $dataMasuk->push([
                    'buku_kode' => $buku->buku_kode,
                    'tipe_display' => 'Cek Masuk',
                    'jenis_display' => "Cek {$buku->jenis_buku} Lembar",
                    'nomor_seri_range' => $buku->nomor_seri_awal . ' - ' . $buku->nomor_seri_akhir,
                    'info_mixed' => $buku->keterangan ?? '-',
                    'tanggal_mixed' => $buku->tanggal_terbit ? Carbon::parse($buku->tanggal_terbit)->translatedFormat('d F Y') : '-',
                    'sort_date' => $buku->created_at
                ]);
            }

            $allData = $dataPenggunaan->merge($dataMasuk)->sortByDesc('sort_date')->values();

            return DataTables::of($allData)
                ->addIndexColumn()
                ->make(true);
        }
    }

    /**
     * Summary statistik untuk dashboard laporan
     */
    public function getSummaryData(Request $request)
    {
        $tanggalDari = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $jenisBuku = $request->jenis_buku;
        $queryTerpakai = LembarCek::with('bukuCek')->where('status', 'terpakai');

        if ($tanggalDari) {
            $queryTerpakai->whereDate('tanggal_pakai', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $queryTerpakai->whereDate('tanggal_pakai', '<=', $tanggalSampai);
        }
        if ($jenisBuku) {
            $queryTerpakai->whereHas('bukuCek', function ($q) use ($jenisBuku) {
                $q->where('jenis_buku', $jenisBuku);
            });
        }

        $queryMasuk = BukuCek::query();
        if ($tanggalDari) {
            $queryMasuk->whereDate('created_at', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $queryMasuk->whereDate('created_at', '<=', $tanggalSampai);
        }
        if ($jenisBuku) {
            $queryMasuk->where('jenis_buku', $jenisBuku);
        }

        $cekTerpakai = $queryTerpakai->get();
        $bukuMasuk = $queryMasuk->get();
        $totalTerpakai = $cekTerpakai->count();
        $totalLembarMasuk = $bukuMasuk->sum('jumlah_lembar');
        $totalBukuMasuk = $bukuMasuk->count();
        $totalNominal = $cekTerpakai->sum('nominal');

        return response()->json([
            'total_terpakai' => $totalTerpakai,
            'total_lembar_masuk' => $totalLembarMasuk,
            'total_buku_masuk' => $totalBukuMasuk,
            'total_nominal' => $totalNominal
        ]);
    }


    public function exportPDF(Request $request)
    {
        $tanggalDari = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $jenisBuku = $request->jenis_buku;
        $tipelaporan = $request->tipe ?? 'penggunaan';

        if ($tipelaporan == 'penggunaan') {
            $query = BukuCek::with(['lembarTerpakai'])
                ->whereHas('lembarTerpakai', function ($q) use ($request) {
                    $q->where('status', 'terpakai');

                    if ($request->filled('tanggal_dari')) {
                        $q->whereDate('tanggal_pakai', '>=', $request->tanggal_dari);
                    }
                    if ($request->filled('tanggal_sampai')) {
                        $q->whereDate('tanggal_pakai', '<=', $request->tanggal_sampai);
                    }
                })
                ->orderBy('created_at', 'desc');

            if ($jenisBuku) {
                $query->where('jenis_buku', $jenisBuku);
            }
        } else {
            $query = BukuCek::with(['lembarTersedia', 'lembarTerpakai'])
                ->orderBy('created_at', 'desc');

            if ($tanggalDari) {
                $query->whereDate('created_at', '>=', $tanggalDari);
            }
            if ($tanggalSampai) {
                $query->whereDate('created_at', '<=', $tanggalSampai);
            }
            if ($jenisBuku) {
                $query->where('jenis_buku', $jenisBuku);
            }
        }

        $data = $query->get();
        $summaryRequest = new Request([
            'tanggal_dari' => $tanggalDari,
            'tanggal_sampai' => $tanggalSampai,
            'jenis_buku' => $jenisBuku
        ]);
        $summary = json_decode($this->getSummaryData($summaryRequest)->getContent(), true);

        $pdf = Pdf::loadView('Admin.LaporanCek.pdf', [
            'data' => $data,
            'summary' => $summary,
            'filters' => [
                'tanggal_dari' => $tanggalDari,
                'tanggal_sampai' => $tanggalSampai,
                'jenis_buku' => $jenisBuku,
                'tipe' => $tipelaporan
            ],
            'tanggal_cetak' => now()->translatedFormat('d F Y H:i')
        ]);

        $filename = 'laporan-' . $tipelaporan . '-cek-' . date('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }

    public function stokIndex()
    {
        $title = "Laporan Stok Cek Keseluruhan";
        return view('Admin.LaporanCek.stok', compact('title'));
    }

    public function getStokDataSimple(Request $request)
    {
        try {
            // Ambil parameter tanggal dari request
            $tanggalMulai = $request->get('tanggal_mulai');
            $tanggalAkhir = $request->get('tanggal_akhir');

            // Validasi tanggal - jika tidak ada, gunakan default
            if (!$tanggalMulai) {
                $tanggalMulai = date('Y-m-01'); // Awal bulan ini
            }
            if (!$tanggalAkhir) {
                $tanggalAkhir = date('Y-m-d'); // Hari ini
            }

            // Debug log
            \Log::info('Filter tanggal stok:', [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_akhir' => $tanggalAkhir
            ]);

            // Step 1: Hitung total buku masuk per jenis dalam periode tertentu
            $queryBukuMasuk = DB::table('buku_cek')
                ->select('jenis_buku', DB::raw('COUNT(*) as total_masuk'))
                ->whereDate('created_at', '>=', $tanggalMulai)
                ->whereDate('created_at', '<=', $tanggalAkhir)
                ->groupBy('jenis_buku');

            $bukuMasuk = $queryBukuMasuk->get()->keyBy('jenis_buku');

            // Step 2: Hitung total buku keluar per jenis dalam periode tertentu
            // Buku dianggap "keluar" jika ada lembar cek dari buku tersebut yang statusnya 'terpakai' dalam periode ini
            $queryBukuKeluar = DB::table('buku_cek as bc')
                ->join('lembar_cek as lc', 'bc.buku_id', '=', 'lc.buku_id')
                ->select('bc.jenis_buku', DB::raw('COUNT(DISTINCT bc.buku_id) as total_keluar'))
                ->where('lc.status', 'terpakai')
                ->whereDate('lc.tanggal_pakai', '>=', $tanggalMulai)
                ->whereDate('lc.tanggal_pakai', '<=', $tanggalAkhir)
                ->groupBy('bc.jenis_buku');

            $bukuKeluar = $queryBukuKeluar->get()->keyBy('jenis_buku');

            // Step 3: Untuk menghitung saldo akhir yang akurat, kita perlu:
            // - Total stok awal (semua buku yang dibuat sebelum periode + dalam periode)
            // - Total yang keluar hingga akhir periode
            $stokAwalQuery = DB::table('buku_cek')
                ->select('jenis_buku', DB::raw('COUNT(*) as stok_awal'))
                ->whereDate('created_at', '<=', $tanggalAkhir)
                ->groupBy('jenis_buku');

            $stokAwal = $stokAwalQuery->get()->keyBy('jenis_buku');

            $totalKeluarQuery = DB::table('buku_cek as bc')
                ->join('lembar_cek as lc', 'bc.buku_id', '=', 'lc.buku_id')
                ->select('bc.jenis_buku', DB::raw('COUNT(DISTINCT bc.buku_id) as total_keluar_semua'))
                ->where('lc.status', 'terpakai')
                ->whereDate('lc.tanggal_pakai', '<=', $tanggalAkhir)
                ->groupBy('bc.jenis_buku');

            $totalKeluar = $totalKeluarQuery->get()->keyBy('jenis_buku');
            $jenisDefault = ['5', '10', '25'];
            $stokData = [];

            foreach ($jenisDefault as $jenis) {
                $masukData = $bukuMasuk->get($jenis);
                $keluarData = $bukuKeluar->get($jenis);
                $stokAwalData = $stokAwal->get($jenis);
                $totalKeluarData = $totalKeluar->get($jenis);
                $totalMasukPeriode = $masukData ? $masukData->total_masuk : 0;
                $totalKeluarPeriode = $keluarData ? $keluarData->total_keluar : 0;
                $stokAwalJumlah = $stokAwalData ? $stokAwalData->stok_awal : 0;
                $totalKeluarJumlah = $totalKeluarData ? $totalKeluarData->total_keluar_semua : 0;
                $saldoAkhir = $stokAwalJumlah - $totalKeluarJumlah;
                $stokData[] = (object)[
                    'jenis_buku' => $jenis,
                    'total_masuk' => $totalMasukPeriode,
                    'total_keluar' => $totalKeluarPeriode,
                    'saldo_akhir' => max(0, $saldoAkhir)
                ];
            }

            \Log::info('Hasil stok data:', $stokData);

            return response()->json([
                'success' => true,
                'data' => $stokData,
                'debug' => [
                    'periode' => "{$tanggalMulai} - {$tanggalAkhir}",
                    'query_masuk' => $queryBukuMasuk->toSql(),
                    'query_keluar' => $queryBukuKeluar->toSql()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getStokDataSimple:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data: ' . $e->getMessage(),
                'data' => [
                    (object)[
                        'jenis_buku' => '5',
                        'total_masuk' => 0,
                        'total_keluar' => 0,
                        'saldo_akhir' => 0
                    ],
                    (object)[
                        'jenis_buku' => '10',
                        'total_masuk' => 0,
                        'total_keluar' => 0,
                        'saldo_akhir' => 0
                    ],
                    (object)[
                        'jenis_buku' => '25',
                        'total_masuk' => 0,
                        'total_keluar' => 0,
                        'saldo_akhir' => 0
                    ]
                ]
            ], 200);
        }
    }
}
