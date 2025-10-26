<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\CustomerModel;
use App\Models\Admin\KategoriModel;
use App\Models\Admin\MerkModel;
use App\Models\Admin\SatuanModel;
use App\Models\Admin\UserModel;
use App\Models\BukuCek;
use App\Models\LembarCek;
use App\Models\StokCek;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data["title"] = "Dashboard";
        $data["kategori"] = KategoriModel::orderBy('kategori_id', 'DESC')->count();
        $data["barang"] = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')->orderBy('barang_id', 'DESC')->count();
        $data["bm"] = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->orderBy('bm_id', 'DESC')->count();
        $data["bk"] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')->orderBy('bk_id', 'DESC')->count();
        $data["user"] = UserModel::leftJoin('tbl_role', 'tbl_role.role_id', '=', 'tbl_user.role_id')->select()->orderBy('user_id', 'DESC')->count();
        $data["total_buku_cek"] = BukuCek::count();
        $data["buku_cek_aktif"] = BukuCek::where('status', 'aktif')->count();
        $data["total_lembar_tersedia"] = LembarCek::where('status', 'tersedia')->count();
        $data["total_lembar_terpakai"] = LembarCek::where('status', 'terpakai')->count();
        $data["penggunaan_bulan_ini"] = LembarCek::where('status', 'terpakai')
            ->whereMonth('tanggal_pakai', date('m'))
            ->whereYear('tanggal_pakai', date('Y'))
            ->count();
        $data["stok_cek"] = StokCek::all();

        return view('Admin.Dashboard.index', $data);
    }
}
