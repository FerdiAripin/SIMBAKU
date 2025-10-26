<?php

use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\BarangkeluarController;
use App\Http\Controllers\Admin\BarangmasukController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LapBarangKeluarController;
use App\Http\Controllers\Admin\LapBarangMasukController;
use App\Http\Controllers\Admin\LapStokBarangController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProdukBaruController;
use App\Http\Controllers\Admin\ProdukLamaController;
use App\Http\Controllers\Master\AksesController;
use App\Http\Controllers\Master\AppreanceController;
use App\Http\Controllers\Master\MenuController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// login admin
Route::middleware(['preventBackHistory'])->group(function () {
    Route::get('/admin/login', [LoginController::class, 'index'])->middleware('useractive');
    Route::post('/admin/proseslogin', [LoginController::class, 'proseslogin'])->middleware('useractive');
    Route::get('/admin/logout', [LoginController::class, 'logout']);
});

// admin
Route::group(['middleware' => 'userlogin'], function () {

    // Profile
    Route::get('/admin/profile/{user}', [UserController::class, 'profile']);
    Route::post('/admin/updatePassword/{user}', [UserController::class, 'updatePassword']);
    Route::post('/admin/updateProfile/{user}', [UserController::class, 'updateProfile']);
    Route::get('/admin/appreance/', [AppreanceController::class, 'index']);
    Route::post('/admin/appreance/{setting}', [AppreanceController::class, 'update']);

    Route::middleware(['checkRoleUser:/dashboard,menu'])->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/admin', [DashboardController::class, 'index']);
        Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    });

    Route::middleware(['checkRoleUser:/kategori,submenu'])->group(function () {
        // Kategori
        Route::get('/admin/kategori', [KategoriController::class, 'index']);
        Route::get('/admin/kategori/show/', [KategoriController::class, 'show'])->name('kategori.getkategori');
        Route::post('/admin/kategori/proses_tambah/', [KategoriController::class, 'proses_tambah'])->name('kategori.store');
        Route::post('/admin/kategori/proses_ubah/{kategori}', [KategoriController::class, 'proses_ubah']);
        Route::post('/admin/kategori/proses_hapus/{kategori}', [KategoriController::class, 'proses_hapus']);
    });

    Route::middleware(['checkRoleUser:/produk_baru,submenu'])->group(function () {
        // Produk Baru
        Route::resource('/admin/produk_baru', \App\Http\Controllers\Admin\ProdukBaruController::class);
        Route::get('/admin/produk_baru/show/', [ProdukBaruController::class, 'show'])->name('produkbaru.getprodukbaru');
        Route::post('/admin/produk_baru/proses_tambah/', [ProdukBaruController::class, 'proses_tambah'])->name('produkbaru.store');
        Route::get('/admin/produk_baru/detail/{barang}', [ProdukBaruController::class, 'detail']);
        Route::post('/admin/produk_baru/proses_ubah/{barang}', [ProdukBaruController::class, 'proses_ubah']);
        Route::post('/admin/produk_baru/proses_hapus/{barang}', [ProdukBaruController::class, 'proses_hapus']);
    });

    Route::middleware(['checkRoleUser:/produk_lama,submenu'])->group(function () {
        // Produk Lama
        Route::resource('/admin/produk_lama', \App\Http\Controllers\Admin\ProdukLamaController::class);
        Route::get('/admin/produk_lama/show/', [ProdukLamaController::class, 'show'])->name('produklama.getproduklama');
        Route::post('/admin/produk_lama/proses_tambah/', [ProdukLamaController::class, 'proses_tambah'])->name('produklama.store');
        Route::get('/admin/produk_lama/detail/{barang}', [ProdukLamaController::class, 'detail']);
        Route::post('/admin/produk_lama/proses_ubah/{barang}', [ProdukLamaController::class, 'proses_ubah']);
        Route::post('/admin/produk_lama/proses_hapus/{barang}', [ProdukLamaController::class, 'proses_hapus']);
    });

    Route::middleware(['checkRoleUser:/masuk,submenu'])->group(function () {
        // Barang Masuk
        Route::resource('/admin/masuk', \App\Http\Controllers\Admin\BarangmasukController::class);
        Route::get('/admin/masuk/show/', [BarangmasukController::class, 'show'])->name('barang-masuk.getbarang-masuk');
        Route::post('/admin/masuk/proses_tambah/', [BarangmasukController::class, 'proses_tambah'])->name('barang-masuk.store');
        Route::post('/admin/masuk/proses_ubah/{barangmasuk}', [BarangmasukController::class, 'proses_ubah']);
        Route::post('/admin/masuk/proses_hapus/{barangmasuk}', [BarangmasukController::class, 'proses_hapus']);
        Route::get('/admin/barang/getbarang/{id}', [BarangController::class, 'getbarang']);
        Route::get('/admin/barang/listbarang/{param}', [BarangController::class, 'listbarang']);
        Route::post('/admin/barang-masuk/proses_hapus/{id}', [BarangMasukController::class, 'destroy'])->name('barang-masuk.proses-hapus');
    });

    Route::middleware(['checkRoleUser:/keluar,submenu'])->group(function () {
        // Barang Keluar
        Route::resource('/admin/keluar', \App\Http\Controllers\Admin\BarangkeluarController::class);
        Route::get('/admin/keluar/show/', [BarangkeluarController::class, 'show'])->name('barang-keluar.getbarang-keluar');
        Route::post('/admin/keluar/proses_tambah/', [BarangkeluarController::class, 'proses_tambah'])->name('barang-keluar.store');
        Route::post('/admin/keluar/proses_ubah/{barangkeluar}', [BarangkeluarController::class, 'proses_ubah']);
        Route::post('/admin/keluar/proses_hapus/{barangkeluar}', [BarangkeluarController::class, 'proses_hapus']);
    });

    Route::middleware(['checkRoleUser:/laporan-masuk,submenu'])->group(function () {
        // Laporan Barang Masuk
        Route::resource('/admin/laporan-masuk', \App\Http\Controllers\Admin\LapBarangMasukController::class);
        Route::get('/admin/lapbarangmasuk/print/', [LapBarangMasukController::class, 'print'])->name('lap-bm.print');
        Route::get('/admin/lapbarangmasuk/pdf/', [LapBarangMasukController::class, 'pdf'])->name('lap-bm.pdf');
        Route::get('/admin/lap-barang-masuk/show/', [LapBarangMasukController::class, 'show'])->name('lap-bm.getlap-bm');
    });

    Route::middleware(['checkRoleUser:/laporan-keluar,submenu'])->group(function () {
        // Laporan Barang Keluar
        Route::resource('/admin/laporan-keluar', \App\Http\Controllers\Admin\LapBarangKeluarController::class);
        Route::get('/admin/lapbarangkeluar/print/', [LapBarangKeluarController::class, 'print'])->name('lap-bk.print');
        Route::get('/admin/lapbarangkeluar/pdf/', [LapBarangKeluarController::class, 'pdf'])->name('lap-bk.pdf');
        Route::get('/admin/lap-barang-keluar/show/', [LapBarangKeluarController::class, 'show'])->name('lap-bk.getlap-bk');
    });

    Route::middleware(['checkRoleUser:/laporan-stok,submenu'])->group(function () {
        // Laporan Stok Barang
        Route::resource('/admin/laporan-stok', \App\Http\Controllers\Admin\LapStokBarangController::class);
        Route::get('/admin/lapstokbarang/print/', [LapStokBarangController::class, 'print'])->name('lap-sb.print');
        Route::get('/admin/lapstokbarang/pdf/', [LapStokBarangController::class, 'pdf'])->name('lap-sb.pdf');
        Route::get('/admin/lap-stok-barang/show/', [LapStokBarangController::class, 'show'])->name('lap-sb.getlap-sb');
    });

    Route::middleware(['checkRoleUser:1,othermenu'])->group(function () {

        Route::middleware(['checkRoleUser:2,othermenu'])->group(function () {
            // Menu
            Route::resource('/admin/menu', \App\Http\Controllers\Master\MenuController::class);
            Route::post('/admin/menu/hapus', [MenuController::class, 'hapus']);
            Route::get('/admin/menu/sortup/{sort}', [MenuController::class, 'sortup']);
            Route::get('/admin/menu/sortdown/{sort}', [MenuController::class, 'sortdown']);
        });

        Route::middleware(['checkRoleUser:3,othermenu'])->group(function () {
            // Role
            Route::resource('/admin/role', \App\Http\Controllers\Master\RoleController::class);
            Route::get('/admin/role/show/', [RoleController::class, 'show'])->name('role.getrole');
            Route::post('/admin/role/hapus', [RoleController::class, 'hapus']);
        });

        Route::middleware(['checkRoleUser:4,othermenu'])->group(function () {
            // List User
            Route::resource('/admin/user', \App\Http\Controllers\Master\UserController::class);
            Route::get('/admin/user/show/', [UserController::class, 'show'])->name('user.getuser');
            Route::post('/admin/user/hapus', [UserController::class, 'hapus']);
        });

        Route::middleware(['checkRoleUser:5,othermenu'])->group(function () {
            // Akses
            Route::get('/admin/akses/{role}', [AksesController::class, 'index']);
            Route::get('/admin/akses/addAkses/{idmenu}/{idrole}/{type}/{akses}', [AksesController::class, 'addAkses']);
            Route::get('/admin/akses/removeAkses/{idmenu}/{idrole}/{type}/{akses}', [AksesController::class, 'removeAkses']);
            Route::get('/admin/akses/setAll/{role}', [AksesController::class, 'setAllAkses']);
            Route::get('/admin/akses/unsetAll/{role}', [AksesController::class, 'unsetAllAkses']);
        });

        Route::middleware(['checkRoleUser:6,othermenu'])->group(function () {
            // Web
            Route::resource('/admin/web', \App\Http\Controllers\Master\WebController::class);
        });

        Route::post('admin/barang-keluar/check-stock', [BarangkeluarController::class, 'checkStock'])->name('barang-keluar.check-stock');

        Route::middleware(['checkRoleUser:/cek,submenu'])->group(function () {
            // Route untuk buku cek (Data Buku Cek)
            Route::get('/admin/cek', [App\Http\Controllers\Admin\CekController::class, 'index']);
            Route::get('/admin/cek/data', [App\Http\Controllers\Admin\CekController::class, 'getCek'])->name('cek.getcek');
            Route::post('/admin/cek', [App\Http\Controllers\Admin\CekController::class, 'store'])->name('cek.store');
            Route::get('/admin/cek/{id}', [App\Http\Controllers\Admin\CekController::class, 'show'])->name('cek.show');
            Route::get('/admin/cek/{id}/edit', [App\Http\Controllers\Admin\CekController::class, 'edit'])->name('cek.edit');
            Route::put('/admin/cek/{id}', [App\Http\Controllers\Admin\CekController::class, 'update'])->name('cek.update');
            Route::delete('/admin/cek/{id}', [App\Http\Controllers\Admin\CekController::class, 'destroy'])->name('cek.destroy');
            Route::get('/admin/cek/stok-jenis/{jenis}', [App\Http\Controllers\Admin\CekController::class, 'getStokByJenis'])->name('cek.stok-jenis');
            Route::post('/admin/cek/gunakan-buku', [App\Http\Controllers\Admin\CekController::class, 'gunakanBuku'])->name('admin.cek.gunakan-buku');
            Route::post('admin/cek/validate-range-nomor-seri', [App\Http\Controllers\Admin\CekController::class, 'validateRangeNomorSeri'])->name('admin.cek.validate-range-nomor-seri');
        });



        Route::middleware(['checkRoleUser:/cek/stok/dashboard,submenu'])->group(function () {
            // Route untuk stok cek
            Route::get('/admin/cek/stok/dashboard', [App\Http\Controllers\Admin\CekController::class, 'stok']);
            Route::get('/admin/cek/stok/api', [App\Http\Controllers\Admin\CekController::class, 'getStokApi'])->name('cek.stok.api');
        });

        Route::middleware(['checkRoleUser:/laporan-cek,submenu'])->group(function () {

            // LAPORAN CEK LENGKAP
            Route::prefix('admin/laporan-cek')->name('laporan-cek.')->group(function () {

                // Halaman utama laporan
                Route::get('/', [App\Http\Controllers\Admin\LaporanCekController::class, 'index'])->name('index');

                // Data untuk DataTables
                Route::get('/penggunaan', [App\Http\Controllers\Admin\LaporanCekController::class, 'getLaporanPenggunaan'])->name('penggunaan');
                Route::get('/masuk', [App\Http\Controllers\Admin\LaporanCekController::class, 'getLaporanMasuk'])->name('masuk');

                // Summary data
                Route::get('/summary', [App\Http\Controllers\Admin\LaporanCekController::class, 'getSummaryData'])->name('summary');

                // Export
                Route::get('/export-pdf', [App\Http\Controllers\Admin\LaporanCekController::class, 'exportPDF'])->name('export-pdf');
                Route::get('/export-excel', [App\Http\Controllers\Admin\LaporanCekController::class, 'exportExcel'])->name('export-excel');

                // LAPORAN STOK
                Route::get('/stok', [App\Http\Controllers\Admin\LaporanCekController::class, 'laporanStok'])->name('stok');
                Route::get('/stok-data', [App\Http\Controllers\Admin\LaporanCekController::class, 'getDataStok'])->name('stok-data');
                Route::get('/stok-pdf', [App\Http\Controllers\Admin\LaporanCekController::class, 'exportStokPDF'])->name('stok-pdf');

                // Chart data
                Route::get('/chart-data', [App\Http\Controllers\Admin\LaporanCekController::class, 'getChartData'])->name('chart-data');
            });

            Route::get('/admin/cek/laporan', [App\Http\Controllers\Admin\CekController::class, 'laporan']);
            Route::get('/admin/cek/laporan/data', [App\Http\Controllers\Admin\CekController::class, 'getLaporanData'])->name('cek.laporan.data');
            Route::get('/admin/cek/laporan/export', [App\Http\Controllers\Admin\CekController::class, 'exportLaporan'])->name('cek.laporan.export');
        });

        Route::middleware(['checkRoleUser:/laporan-stok-cek,submenu'])->group(function () {
            // Route untuk halaman utama laporan stok
            Route::get('/admin/laporan-stok-cek', [App\Http\Controllers\Admin\LaporanCekController::class, 'stokIndex'])->name('laporan-stok-cek');

            // Route untuk mendapatkan data AJAX
            Route::get('/laporan-stok-cek/data', [App\Http\Controllers\Admin\LaporanCekController::class, 'getStokDataSimple'])->name('laporan-stok-cek.data');
        });

        Route::prefix('api')->group(function () {
            Route::get('/cek/validate/{nomor_seri}', [App\Http\Controllers\Admin\CekController::class, 'validateNomorSeri']);
            Route::get('/cek/statistik', [App\Http\Controllers\Admin\CekController::class, 'getStatistik']);
            Route::post('/cek/check-nomor-range', [App\Http\Controllers\Admin\CekController::class, 'checkNomorSeriRange'])->name('cek.check.range');
            Route::get('/cek/validate/{nomor_seri}', [App\Http\Controllers\Admin\CekController::class, 'validateNomorSeri'])->name('cek.validate');
        });
    });
});
