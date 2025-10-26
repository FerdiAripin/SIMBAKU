<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\WebModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;

class LapStokBarangController extends Controller
{
    public function index(Request $request)
    {
        $data["title"] = "Laporan Stok";
        $data["kategori"] = KategoriModel::get();
        return view('Admin.Laporan.StokBarang.index', $data);
    }

    public function print(Request $request)
    {
        $query = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')
            ->orderBy('barang_id', 'DESC');

        if ($request->jenis) {
            $query->where('barang_type', $request->jenis);
        }

        if ($request->kategori) {
            $query->where('tbl_barang.kategori_id', $request->kategori);
        }

        $data['data'] = $query->get();
        $data["title"] = "Print Stok Barang";
        $data['web'] = WebModel::first();
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        $data['jenis'] = $request->jenis ?? 'baru';
        $data['kategori_nama'] = null;
        if ($request->kategori) {
            $kategori = KategoriModel::find($request->kategori);
            $data['kategori_nama'] = $kategori ? $kategori->kategori_nama : null;
        }

        return view('Admin.Laporan.StokBarang.print', $data);
    }

    public function pdf(Request $request)
    {
        $query = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')
            ->orderBy('barang_id', 'DESC');

        if ($request->jenis) {
            $query->where('barang_type', $request->jenis);
        }

        if ($request->kategori) {
            $query->where('tbl_barang.kategori_id', $request->kategori);
        }

        $data['data'] = $query->get();

        $data["title"] = "PDF Stok Barang";
        $data['web'] = WebModel::first();
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        $data['jenis'] = $request->jenis ?? 'baru';
        $data['kategori_nama'] = null;
        if ($request->kategori) {
            $kategori = KategoriModel::find($request->kategori);
            $data['kategori_nama'] = $kategori ? $kategori->kategori_nama : null;
        }

        try {
            $pdf = PDF::loadView('Admin.Laporan.StokBarang.pdf', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }

        $filename = 'lap-stok-' . $data['jenis'];
        if ($request->tglawal) {
            $filename .= '-' . $request->tglawal . '-' . $request->tglakhir;
        } else {
            $filename .= '-semua-tanggal';
        }

        if ($request->kategori) {
            $filename .= '-kategori-' . $request->kategori;
        }

        return $pdf->download($filename . '.pdf');
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')
                ->orderBy('barang_id', 'DESC');

            if ($request->jenis) {
                $query->where('barang_type', $request->jenis);
            }

            if ($request->kategori) {
                $query->where('tbl_barang.kategori_id', $request->kategori);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori', function ($row) {
                    $kategori = $row->kategori_nama == '' ? '-' : $row->kategori_nama;
                    return $kategori;
                })
                ->addColumn('stokawal', function ($row) {
                    $result = '<span class="">' . $row->barang_stok . '</span>';
                    return $result;
                })
                ->addColumn('jmlmasuk', function ($row) use ($request) {
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    }

                    $result = '<span class="">' . $jmlmasuk . '</span>';
                    return $result;
                })
                ->addColumn('jmlkeluar', function ($row) use ($request) {
                    if ($request->tglawal) {
                        $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode)->sum('tbl_barangkeluar.bk_jumlah');
                    } else {
                        $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode)->sum('tbl_barangkeluar.bk_jumlah');
                    }

                    $result = '<span class="">' . $jmlkeluar . '</span>';
                    return $result;
                })
                ->addColumn('totalstok', function ($row) use ($request) {
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    }

                    if ($request->tglawal) {
                        $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode)->sum('tbl_barangkeluar.bk_jumlah');
                    } else {
                        $jmlkeluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode)->sum('tbl_barangkeluar.bk_jumlah');
                    }

                    $totalstok = $row->barang_stok + ($jmlmasuk - $jmlkeluar);
                    if ($totalstok == 0) {
                        $result = '<span class="">' . $totalstok . '</span>';
                    } else if ($totalstok > 0) {
                        $result = '<span class="text-success">' . $totalstok . '</span>';
                    } else {
                        $result = '<span class="text-danger">' . $totalstok . '</span>';
                    }

                    return $result;
                })
                ->rawColumns(['kategori', 'stokawal', 'jmlmasuk', 'jmlkeluar', 'totalstok'])->make(true);
        }
    }
}
