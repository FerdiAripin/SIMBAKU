<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    public function index()
    {
        $data["title"] = "Kategori";
        $data["hakTambah"] = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Kategori', 'tbl_akses.akses_type' => 'create'))->count();
        return view('Admin.Kategori.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = KategoriModel::orderBy('kategori_id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('ket', function ($row) {
                    $ket = $row->kategori_ket == '' ? '-' : $row->kategori_ket;

                    return $ket;
                })
                ->addColumn('action', function ($row) {
                    $array = array(
                        "kategori_id" => $row->kategori_id,
                        "kategori_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->kategori_nama)),
                        "kategori_ket" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->kategori_ket)),
                    );
                    $button = '';
                    $hakEdit = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Kategori', 'tbl_akses.akses_type' => 'update'))->count();
                    $hakDelete = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Kategori', 'tbl_akses.akses_type' => 'delete'))->count();
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
                ->rawColumns(['action', 'ket'])->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|unique:tbl_kategori,kategori_nama',
            'ket' => 'nullable'
        ], [
            'kategori.required' => 'Kategori wajib di isi!',
            'kategori.unique' => 'Kategori sudah ada, gunakan nama lain!'
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->kategori)));

        //create
        KategoriModel::create([
            'kategori_nama' => $request->kategori,
            'kategori_slug'   => $slug,
            'kategori_ket' => $request->ket
        ]);

        return response()->json(['status' => 'success', 'message' => 'Berhasil ditambahkan']);
    }

    public function proses_ubah(Request $request, KategoriModel $kategori)
    {
        // Validasi input - ignore kategori yang sedang diupdate
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|unique:tbl_kategori,kategori_nama,' . $kategori->kategori_id . ',kategori_id',
            'ket' => 'nullable'
        ], [
            'kategori.required' => 'Kategori wajib di isi!',
            'kategori.unique' => 'Kategori sudah ada, gunakan nama lain!'
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->kategori)));

        //update
        $kategori->update([
            'kategori_nama' => $request->kategori,
            'kategori_slug'   => $slug,
            'kategori_ket' => $request->ket
        ]);

        return response()->json(['status' => 'success', 'message' => 'Berhasil diupdate']);
    }

    public function proses_hapus(Request $request, KategoriModel $kategori)
    {

        //delete
        $kategori->delete();

        return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus']);
    }
}
