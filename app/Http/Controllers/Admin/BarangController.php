<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $data["title"] = "Barang";
        $data["hakTambah"] = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Barang', 'tbl_akses.akses_type' => 'create'))->count();
        $data["kategori"] =  KategoriModel::orderBy('kategori_id', 'DESC')->get();
        return view('Admin.Barang.index', $data);
    }

    public function getbarang($id)
    {
        $data = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')->where('tbl_barang.barang_kode', '=', $id)->get();
        return json_encode($data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {

            $data = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id')->leftJoin('tbl_satuan', 'tbl_satuan.satuan_id', '=', 'tbl_barang.satuan_id')->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')->orderBy('barang_id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('img', function ($row) {
                    $array = array(
                        "barang_gambar" => $row->barang_gambar,
                    );
                    if ($row->barang_gambar == "image.png") {
                        $img = '<a data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Gmodaldemo8" onclick=gambar(' . json_encode($array) . ')><span class="avatar avatar-lg cover-image" style="background: url(&quot;' . url('/assets/default/barang') . '/' . $row->barang_gambar . '&quot;) center center;"></span></a>';
                    } else {
                        $img = '<a data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#Gmodaldemo8" onclick=gambar(' . json_encode($array) . ')><span class="avatar avatar-lg cover-image" style="background: url(&quot;' . asset('storage/barang/' . $row->barang_gambar) . '&quot;) center center;"></span></a>';
                    }

                    return $img;
                })
                ->addColumn('kategori', function ($row) {
                    $kategori = $row->kategori_id == '' ? '-' : $row->kategori_nama;

                    return $kategori;
                })
                ->addColumn('satuan', function ($row) {
                    $satuan = $row->satuan_id == '' ? '-' : $row->satuan_nama;

                    return $satuan;
                })
                ->addColumn('merk', function ($row) {
                    $merk = $row->merk_id == '' ? '-' : $row->merk_nama;

                    return $merk;
                })
                ->addColumn('totalstok', function ($row) use ($request) {
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->leftJoin('tbl_customer', 'tbl_customer.customer_id', '=', 'tbl_barangmasuk.customer_id')->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')->leftJoin('tbl_customer', 'tbl_customer.customer_id', '=', 'tbl_barangmasuk.customer_id')->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])->where('tbl_barangmasuk.barang_kode', '=', $row->barang_kode)->sum('tbl_barangmasuk.bm_jumlah');
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
                ->addColumn('action', function ($row) {
                    $array = array(
                        "barang_id" => $row->barang_id,
                        "kategori_id" => $row->kategori_id,
                        "satuan_id" => $row->satuan_id,
                        "merk_id" => $row->merk_id,
                        "barang_kode" => $row->barang_kode,
                        "barang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->barang_nama)),
                        "barang_stok" => $row->barang_stok,
                        "barang_gambar" => $row->barang_gambar,
                    );
                    $button = '';
                    $hakEdit = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Barang', 'tbl_akses.akses_type' => 'update'))->count();
                    $hakDelete = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')->where(array('tbl_akses.role_id' => Session::get('user')->role_id, 'tbl_submenu.submenu_judul' => 'Barang', 'tbl_akses.akses_type' => 'delete'))->count();
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
                ->rawColumns(['action', 'img', 'kategori', 'satuan', 'merk', 'totalstok'])->make(true);
        }
    }

    public function listbarang(Request $request)
    {
        if ($request->ajax()) {
            $data = BarangModel::leftJoin('tbl_kategori', 'tbl_kategori.kategori_id', '=', 'tbl_barang.kategori_id');

            if ($request->jenis) {
                $data = $data->where('barang_type', $request->jenis);
            }

            $data = $data->orderBy('barang_id', 'DESC')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('img', function ($row) {
                    if ($row->barang_gambar == "image.png") {
                        $img = '<span class="avatar avatar-lg cover-image" style="background: url(&quot;' . url('/assets/default/barang') . '/' . $row->barang_gambar . '&quot;) center center;"></span>';
                    } else {
                        $img = '<span class="avatar avatar-lg cover-image" style="background: url(&quot;' . asset('storage/barang/' . $row->barang_gambar) . '&quot;) center center;"></span>';
                    }

                    return $img;
                })
                ->addColumn('kategori', function ($row) {
                    $kategori = $row->kategori_id == '' ? '-' : $row->kategori_nama;

                    return $kategori;
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
                ->addColumn('action', function ($row) use ($request) {
                    $array = array(
                        "barang_kode" => $row->barang_kode,
                        "barang_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->barang_nama)),
                        "satuan_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->satuan_nama)),
                        "kategori_nama" => trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $row->kategori_nama)),
                    );
                    $button = '';
                    if ($request->get('param') == 'tambah') {
                        $button .= '
                        <div class="g-2">
                            <a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick=pilihBarang(' . json_encode($array) . ')>Pilih</a>
                        </div>
                        ';
                    } else {
                        $button .= '
                    <div class="g-2">
                        <a class="btn btn-success btn-sm" href="javascript:void(0)" onclick=pilihBarangU(' . json_encode($array) . ')>Pilih</a>
                    </div>
                    ';
                    }

                    return $button;
                })
                ->rawColumns(['action', 'img', 'kategori', 'satuan', 'merk', 'totalstok'])->make(true);
        }
    }

    public function proses_tambah(Request $request)
    {
        $img = "";
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->nama)));

        //upload image
        if ($request->file('foto') == null) {
            $img = "image.png";
        } else {
            $image = $request->file('foto');
            $image->storeAs('public/barang/', $image->hashName());
            $img = $image->hashName();
        }


        //create
        BarangModel::create([
            'barang_gambar' => $img,
            'kategori_id' => $request->kategori,
            'satuan_id' => $request->satuan,
            'merk_id' => $request->merk,
            'barang_kode' => $request->kode,
            'barang_nama' => $request->nama,
            'barang_slug' => $slug,
            'barang_stok' => 0,

        ]);

        return response()->json(['success' => 'Berhasil']);
    }

    public function proses_ubah(Request $request, BarangModel $barang)
    {

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->nama)));

        //check if image is uploaded
        if ($request->hasFile('foto')) {

            //upload new image
            $image = $request->file('foto');
            $image->storeAs('public/barang', $image->hashName());

            //delete old image
            Storage::delete('public/barang/' . $barang->barang_gambar);

            //update data with new image
            $barang->update([
                'barang_gambar'  => $image->hashName(),
                'kategori_id' => $request->kategori,
                'satuan_id' => $request->satuan,
                'merk_id' => $request->merk,
                'barang_kode' => $request->kode,
                'barang_nama' => $request->nama,
                'barang_slug' => $slug,
                'barang_stok' => $request->stok,
            ]);
        } else {
            //update data without image
            $barang->update([
                'kategori_id' => $request->kategori,
                'satuan_id' => $request->satuan,
                'merk_id' => $request->merk,
                'barang_kode' => $request->kode,
                'barang_nama' => $request->nama,
                'barang_slug' => $slug,
                'barang_stok' => $request->stok,
            ]);
        }

        return response()->json(['success' => 'Berhasil']);
    }


    public function proses_hapus(Request $request, BarangModel $barang)
    {
        //delete image
        Storage::delete('public/barang/' . $barang->barang_gambar);

        //delete
        $barang->delete();

        return response()->json(['success' => 'Berhasil']);
    }
}
