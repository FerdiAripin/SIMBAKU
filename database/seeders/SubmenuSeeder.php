<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubmenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_submenu')->insert([
            [
                'submenu_id'     => 9,
                'menu_id'        => '1668510437',
                'submenu_judul'  => 'Masuk',
                'submenu_slug'   => 'masuk',
                'submenu_redirect' => '/masuk',
                'submenu_sort'   => '1',
                'created_at'     => '2022-11-15 11:08:19',
                'updated_at'     => '2022-11-15 11:08:19',
            ],
            [
                'submenu_id'     => 10,
                'menu_id'        => '1668510437',
                'submenu_judul'  => 'Keluar',
                'submenu_slug'   => 'keluar',
                'submenu_redirect' => '/keluar',
                'submenu_sort'   => '2',
                'created_at'     => '2022-11-15 11:08:19',
                'updated_at'     => '2022-11-15 11:08:19',
            ],
            [
                'submenu_id'     => 21,
                'menu_id'        => '1668510568',
                'submenu_judul'  => 'Laporan Masuk',
                'submenu_slug'   => 'laporan-masuk',
                'submenu_redirect' => '/laporan-masuk',
                'submenu_sort'   => '1',
                'created_at'     => '2022-11-30 12:56:24',
                'updated_at'     => '2022-11-30 12:56:24',
            ],
            [
                'submenu_id'     => 22,
                'menu_id'        => '1668510568',
                'submenu_judul'  => 'Laporan Keluar',
                'submenu_slug'   => 'laporan-keluar',
                'submenu_redirect' => '/laporan-keluar',
                'submenu_sort'   => '2',
                'created_at'     => '2022-11-30 12:56:24',
                'updated_at'     => '2022-11-30 12:56:24',
            ],
            [
                'submenu_id'     => 23,
                'menu_id'        => '1668510568',
                'submenu_judul'  => 'Laporan Stok',
                'submenu_slug'   => 'laporan-stok',
                'submenu_redirect' => '/laporan-stok',
                'submenu_sort'   => '3',
                'created_at'     => '2022-11-30 12:56:24',
                'updated_at'     => '2022-11-30 12:56:24',
            ],
            [
                'submenu_id'     => 33,
                'menu_id'        => '1754316593',
                'submenu_judul'  => 'Produk Baru',
                'submenu_slug'   => 'produk-baru',
                'submenu_redirect' => '/produk_baru',
                'submenu_sort'   => '1',
                'created_at'     => '2025-08-04 14:10:06',
                'updated_at'     => '2025-08-04 14:10:06',
            ],
            [
                'submenu_id'     => 34,
                'menu_id'        => '1754316593',
                'submenu_judul'  => 'Produk Lama',
                'submenu_slug'   => 'produk-lama',
                'submenu_redirect' => '/produk_lama',
                'submenu_sort'   => '2',
                'created_at'     => '2025-08-04 14:10:06',
                'updated_at'     => '2025-08-04 14:10:06',
            ],
            [
                'submenu_id'     => 35,
                'menu_id'        => '1668509889',
                'submenu_judul'  => 'Kategori',
                'submenu_slug'   => 'kategori',
                'submenu_redirect' => '/kategori',
                'submenu_sort'   => '1',
                'created_at'     => '2025-08-13 08:00:01',
                'updated_at'     => '2025-08-13 08:00:01',
            ],
            [
                'submenu_id'     => 36,
                'menu_id'        => '1754316594',
                'submenu_judul'  => 'Kelola Cek',
                'submenu_slug'   => 'data-buku-cek',
                'submenu_redirect' => '/cek',
                'submenu_sort'   => '1',
                'created_at'     => '2025-09-12 08:00:01',
                'updated_at'     => '2025-09-12 08:00:01',
            ],
            [
                'submenu_id'     => 44,
                'menu_id'        => '1754316594',
                'submenu_judul'  => 'Riwayat Cek',
                'submenu_slug'   => 'laporan-lengkap',
                'submenu_redirect' => '/laporan-cek',
                'submenu_sort'   => '1',
                'created_at'     => '2025-09-12 08:00:01',
                'updated_at'     => '2025-09-12 08:00:01',
            ],
            [
                'submenu_id'     => 45,
                'menu_id'        => '1754316594',
                'submenu_judul'  => 'Laporan Stok Cek',
                'submenu_slug'   => 'laporan-stok',
                'submenu_redirect' => '/laporan-stok-cek',
                'submenu_sort'   => '2',
                'created_at'     => '2025-09-12 08:00:01',
                'updated_at'     => '2025-09-12 08:00:01',
            ],
        ]);
    }
}
