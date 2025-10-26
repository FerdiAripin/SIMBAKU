<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\KategoriModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class BarangkeluarController extends Controller
{
    public function index()
    {
        $data["title"] = "Keluar";
        $data["hakTambah"] = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Keluar', 'tbl_akses.akses_type' => 'create'))->count();
        // Tambahkan data kategori untuk filter
        $data["kategori"] = KategoriModel::orderBy('kategori_nama', 'ASC')->get();
        return view('Admin.BarangKeluar.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id');

            // Filter berdasarkan jenis produk (baru/lama)
            if ($request->jenis) {
                $query->where('tbl_barang.barang_type', $request->jenis);
            }

            // Filter berdasarkan kategori
            if ($request->kategori && $request->kategori != '') {
                $query->where('tbl_barang.kategori_id', $request->kategori);
            }

            // Filter berdasarkan status (contoh berdasarkan stok)
            if ($request->status && $request->status != '') {
                if ($request->status == 'tersedia') {
                    $query->where('tbl_barang.barang_stok', '>', 0);
                } elseif ($request->status == 'habis') {
                    $query->where('tbl_barang.barang_stok', '<=', 0);
                }
            }

            $data = $query->orderBy('bk_id', 'DESC')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    $tgl = $row->bk_tanggal == '' ? '-' : Carbon::parse($row->bk_tanggal)->translatedFormat('d F Y');

                    return $tgl;
                })
                ->addColumn('keterangan', function ($row) {
                    $keterangan = $row->bk_keterangan == '' ? '-' : $row->bk_keterangan;

                    return $keterangan;
                })
                ->addColumn('barang', function ($row) {
                    $barang = $row->barang_id == '' ? '-' : $row->barang_nama;

                    return $barang;
                })
                ->addColumn('kategori', function ($row) {
                    $kategori = $row->kategori_id == '' ? '-' : $row->kategori_nama;

                    return $kategori;
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "bk_id" => $row->bk_id,
                        "bk_kode" => $row->bk_kode,
                        "barang_kode" => $row->barang_kode,
                        "bk_tanggal" => $row->bk_tanggal,
                        "bk_keterangan" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->bk_keterangan)),
                        "bk_jumlah" => $row->bk_jumlah
                    );
                    $button = '';
                    $hakEdit = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Keluar', 'tbl_akses.akses_type' => 'update'))->count();
                    $hakDelete = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Keluar', 'tbl_akses.akses_type' => 'delete'))->count();
                    if ($hakEdit > 0 && $hakDelete > 0) {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick=update(' . json_encode($array) . ')><span class="fe fe-edit text-success fs-14"></span></a>
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=hapus(' . json_encode($array) . ')><span class="fe fe-trash-2 fs-14"></span></a>
                        </div>
                        ';
                    } else if ($hakEdit > 0 && $hakDelete == 0) {
                        $button .= '
                        <div class="g-2">
                            <a class="btn modal-effect text-primary btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Umodaldemo8" data-bs-toggle="tooltip" data-bs-original-title="Edit" onclick=update(' . json_encode($array) . ')><span class="fe fe-edit text-success fs-14"></span></a>
                        </div>
                        ';
                    } else if ($hakEdit == 0 && $hakDelete > 0) {
                        $button .= '
                        <div class="g-2">
                        <a class="btn modal-effect text-danger btn-sm" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Hmodaldemo8" onclick=hapus(' . json_encode($array) . ')><span class="fe fe-trash-2 fs-14"></span></a>
                        </div>
                        ';
                    } else {
                        $button .= '-';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'tgl', 'keterangan', 'barang', 'kategori'])->make(true);
        }
    }

    // Method untuk mengecek stok
    public function checkStock(Request $request)
    {
        $barangKode = $request->kode_barang;
        $jumlahKeluar = (int) $request->jumlah;
        $bkId = $request->bk_id ?? null; // mengecualikan data yang sedang diedit

        // Ambil data barang untuk mendapatkan stok awal
        $barang = BarangModel::where('barang_kode', $barangKode)->first();

        if (!$barang) {
            return response()->json(['valid' => false, 'message' => 'Barang tidak ditemukan!']);
        }

        // Hitung total barang masuk (asumsi ada model BarangmasukModel)
        $totalMasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', $barangKode)->sum('bm_jumlah');

        // Hitung total barang keluar (kecuali yang sedang diedit)
        $totalKeluar = BarangkeluarModel::where('barang_kode', $barangKode);

        if ($bkId) {
            $totalKeluar = $totalKeluar->where('bk_id', '!=', $bkId);
        }

        $totalKeluar = $totalKeluar->sum('bk_jumlah');

        // Hitung stok real: stok_awal + total_masuk - total_keluar
        $stokReal = $barang->barang_stok + $totalMasuk - $totalKeluar;

        if ($jumlahKeluar > $stokReal) {
            return response()->json([
                'valid' => false,
                'message' => "Jumlah keluar melebihi stok tersedia! Stok tersisa: {$stokReal}",
                'stok_tersisa' => $stokReal,
                'stok_awal' => $barang->barang_stok,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'stok_real' => $stokReal
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Stok mencukupi',
            'stok_tersisa' => $stokReal - $jumlahKeluar,
            'stok_awal' => $barang->barang_stok,
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'stok_real' => $stokReal
        ]);
    }

    public function proses_tambah(Request $request)
    {
        // Validasi stok sebelum menyimpan
        $barangKode = $request->barang;
        $jumlahKeluar = (int) $request->jml;

        $barang = BarangModel::where('barang_kode', $barangKode)->first();

        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan!'], 400);
        }

        // Hitung total barang masuk
        $totalMasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', $barangKode)->sum('bm_jumlah');

        // Hitung total barang keluar
        $totalKeluar = BarangkeluarModel::where('barang_kode', $barangKode)->sum('bk_jumlah');

        // Hitung stok real
        $stokReal = $barang->barang_stok + $totalMasuk - $totalKeluar;

        if ($jumlahKeluar > $stokReal) {
            return response()->json([
                'error' => "Jumlah keluar melebihi stok tersedia! Stok real: {$stokReal} (Stok awal: {$barang->barang_stok} + Masuk: {$totalMasuk} - Keluar: {$totalKeluar})"
            ], 400);
        }

        //insert data
        BarangkeluarModel::create([
            'bk_tanggal' => $request->tglkeluar,
            'bk_kode' => $request->bkkode,
            'barang_kode' => $request->barang,
            'bk_keterangan'   => $request->keterangan,
            'bk_jumlah'   => $request->jml,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, BarangkeluarModel $barangkeluar)
    {
        // Validasi stok sebelum mengupdate
        $barangKode = $request->barang;
        $jumlahKeluar = (int) $request->jml;

        $barang = BarangModel::where('barang_kode', $barangKode)->first();

        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan!'], 400);
        }

        // Hitung total barang masuk
        $totalMasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', $barangKode)->sum('bm_jumlah');

        // Hitung total keluar kecuali data yang sedang diedit
        $totalKeluar = BarangkeluarModel::where('barang_kode', $barangKode)
            ->where('bk_id', '!=', $barangkeluar->bk_id)
            ->sum('bk_jumlah');

        // Hitung stok real
        $stokReal = $barang->barang_stok + $totalMasuk - $totalKeluar;

        if ($jumlahKeluar > $stokReal) {
            return response()->json([
                'error' => "Jumlah keluar melebihi stok tersedia! Stok real: {$stokReal} (Stok awal: {$barang->barang_stok} + Masuk: {$totalMasuk} - Keluar: {$totalKeluar})"
            ], 400);
        }

        //update data
        $barangkeluar->update([
            'bk_tanggal' => $request->tglkeluar,
            'bk_kode' => $request->bkkode,
            'barang_kode' => $request->barang,
            'bk_keterangan'   => $request->keterangan,
            'bk_jumlah'   => $request->jml,
        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_hapus(Request $request, BarangkeluarModel $barangkeluar)
    {
        //delete
        $barangkeluar->delete();

        return response()->json(['success' => 'Berhasil']);
    }
}
