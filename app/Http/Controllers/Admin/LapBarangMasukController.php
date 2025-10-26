<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\WebModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;

class LapBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $data["title"] = "Laporan Masuk";
        $data["kategori"] = KategoriModel::get();
        return view('Admin.Laporan.BarangMasuk.index', $data);
    }

    public function print(Request $request)
    {
        $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')
            ->orderBy('bm_id', 'DESC');

        if ($request->tglawal) {
            $query->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir]);
        }

        if ($request->jenis) {
            $query->where('tbl_barang.barang_type', $request->jenis);
        }

        if ($request->kategori) {
            $query->where('tbl_kategori.kategori_id', $request->kategori);
        }

        $data['data'] = $query->get();

        $data["title"] = "Print Barang Masuk";
        $data['web'] = WebModel::first();
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        $data['kategori_nama'] = null;
        if ($request->kategori) {
            $kategori = KategoriModel::find($request->kategori);
            $data['kategori_nama'] = $kategori ? $kategori->kategori_nama : null;
        }

        return view('Admin.Laporan.BarangMasuk.print', $data);
    }

    public function pdf(Request $request)
    {
        try {
            \Log::info('PDF Request - Start Processing', [
                'all_params' => $request->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            $jenis = $request->get('jenis');
            if (!$jenis || !in_array($jenis, ['baru', 'lama'])) {
                \Log::error('Invalid jenis parameter', ['jenis' => $jenis]);
                return response()->json([
                    'error' => 'Invalid Parameter',
                    'message' => 'Parameter jenis harus "baru" atau "lama"'
                ], 400);
            }

            \Log::info('Jenis validation passed', ['jenis' => $jenis]);
            \Log::info('Building database query...');

            $query = BarangmasukModel::select([
                'tbl_barangmasuk.*',
                'tbl_barang.barang_nama',
                'tbl_barang.barang_kode as barang_kode_join',
                'tbl_barang.barang_type',
                'tbl_kategori.kategori_nama'
            ])
            ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id');

            \Log::info('Base query built successfully');

            if ($request->filled('tglawal') && $request->filled('tglakhir')) {
                $tglawal = $request->get('tglawal');
                $tglakhir = $request->get('tglakhir');
                $query->whereBetween('tbl_barangmasuk.bm_tanggal', [$tglawal, $tglakhir]);
                \Log::info('Date filter applied', ['tglawal' => $tglawal, 'tglakhir' => $tglakhir]);
            }

            $query->where('tbl_barang.barang_type', $jenis);
            \Log::info('Jenis filter applied', ['jenis' => $jenis]);

            if ($request->filled('kategori')) {
                $kategori = $request->get('kategori');
                $query->where('tbl_kategori.kategori_id', $kategori);
                \Log::info('Category filter applied', ['kategori' => $kategori]);
            }

            $query->orderBy('tbl_barangmasuk.bm_id', 'DESC');
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            \Log::info('Final query', ['sql' => $sql, 'bindings' => $bindings]);
            \Log::info('Executing query...');
            $results = $query->get();
            $resultCount = $results->count();
            \Log::info('Query executed successfully', ['count' => $resultCount]);

            if ($resultCount === 0) {
                \Log::warning('No data found for filters');
            }

            \Log::info('Preparing data for PDF...');

            $data = [
                'data' => $results,
                'title' => 'Laporan Barang Masuk - ' . ucfirst($jenis),
                'tglawal' => $request->get('tglawal'),
                'tglakhir' => $request->get('tglakhir'),
                'jenis' => $jenis,
                'kategori_nama' => null
            ];

            try {
                $webConfig = WebModel::first();
                $data['web'] = $webConfig ?: (object)[
                    'web_nama' => 'Sistem Inventory',
                    'web_deskripsi' => 'Laporan Barang Masuk'
                ];
                \Log::info('Web config loaded');
            } catch (\Exception $e) {
                \Log::error('Failed to load web config', ['error' => $e->getMessage()]);
                $data['web'] = (object)[
                    'web_nama' => 'Sistem Inventory',
                    'web_deskripsi' => 'Laporan Barang Masuk'
                ];
            }

            if ($request->filled('kategori')) {
                try {
                    $kategori = KategoriModel::find($request->get('kategori'));
                    $data['kategori_nama'] = $kategori ? $kategori->kategori_nama : 'Kategori tidak ditemukan';
                    \Log::info('Category name loaded', ['kategori_nama' => $data['kategori_nama']]);
                } catch (\Exception $e) {
                    \Log::error('Failed to load category name', ['error' => $e->getMessage()]);
                    $data['kategori_nama'] = 'Error loading category';
                }
            }

            \Log::info('Data preparation completed');
            \Log::info('Starting PDF generation...');

            $viewName = 'Admin.Laporan.BarangMasuk.pdf';

            if (!\View::exists($viewName)) {
                \Log::error('PDF view not found', ['view' => $viewName]);
                return response()->json([
                    'error' => 'View Error',
                    'message' => 'PDF template tidak ditemukan: ' . $viewName
                ], 500);
            }

            try {
                $pdf = PDF::loadView($viewName, $data);
                \Log::info('PDF view loaded successfully');
                $pdf->setPaper('A4', 'landscape');
                $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
                \Log::info('PDF options set');

            } catch (\Exception $e) {
                \Log::error('PDF generation failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'view' => $viewName
                ]);

                return response()->json([
                    'error' => 'PDF Generation Failed',
                    'message' => 'Gagal membuat PDF: ' . $e->getMessage(),
                    'view' => $viewName,
                    'data_count' => $resultCount
                ], 500);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $filename = "laporan-barang-masuk-{$jenis}-{$timestamp}.pdf";

            if ($request->filled('tglawal') && $request->filled('tglakhir')) {
                $tglawal_clean = str_replace(['/', '-', ' '], '_', $request->get('tglawal'));
                $tglakhir_clean = str_replace(['/', '-', ' '], '_', $request->get('tglakhir'));
                $filename = "laporan-barang-masuk-{$jenis}-{$tglawal_clean}_to_{$tglakhir_clean}-{$timestamp}.pdf";
            }

            \Log::info('PDF filename generated', ['filename' => $filename]);
            \Log::info('Returning PDF download...');
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Unhandled exception in PDF generation', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'System Error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id');

            if ($request->tglawal != '') {
                $query->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir]);
            }

            if ($request->jenis) {
                $query->where('tbl_barang.barang_type', $request->jenis);
            }

            if ($request->kategori) {
                $query->where('tbl_kategori.kategori_id', $request->kategori);
            }

            $data = $query->orderBy('bm_id', 'DESC')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    $tgl = $row->bm_tanggal == '' ? '-' : Carbon::parse($row->bm_tanggal)->translatedFormat('d F Y');
                    return $tgl;
                })
                ->addColumn('barang', function ($row) {
                    $barang = $row->barang_id == '' ? '-' : $row->barang_nama;
                    return $barang;
                })
                ->addColumn('kategori', function ($row) {
                    $kategori = $row->kategori_nama == '' ? '-' : $row->kategori_nama;
                    return $kategori;
                })
                ->rawColumns(['tgl', 'barang', 'kategori'])->make(true);
        }
    }
}
