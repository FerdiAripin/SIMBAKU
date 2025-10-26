<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_menu')->insert([
            [
                'menu_id'      => 1667444041,
                'menu_judul'   => 'Dashboard',
                'menu_slug'    => 'dashboard',
                'menu_icon'    => 'home',
                'menu_redirect'=> '/dashboard',
                'menu_sort'    => '1',
                'menu_type'    => '1',
                'created_at'   => '2022-11-15 10:51:04',
                'updated_at'   => '2025-08-13 08:16:27',
            ],
            [
                'menu_id'      => 1668509889,
                'menu_judul'   => 'Master Data',
                'menu_slug'    => 'master-data',
                'menu_icon'    => 'edit',
                'menu_redirect'=> '-',
                'menu_sort'    => '2',
                'menu_type'    => '2',
                'created_at'   => '2022-11-15 10:58:09',
                'updated_at'   => '2025-08-13 08:16:27',
            ],
            [
                'menu_id'      => 1668510437,
                'menu_judul'   => 'Transaksi',
                'menu_slug'    => 'transaksi',
                'menu_icon'    => 'repeat',
                'menu_redirect'=> '-',
                'menu_sort'    => '4',
                'menu_type'    => '2',
                'created_at'   => '2022-11-15 11:07:17',
                'updated_at'   => '2025-08-13 08:16:27',
            ],
            [
                'menu_id'      => 1668510568,
                'menu_judul'   => 'Laporan',
                'menu_slug'    => 'laporan',
                'menu_icon'    => 'printer',
                'menu_redirect'=> '-',
                'menu_sort'    => '5',
                'menu_type'    => '2',
                'created_at'   => '2022-11-15 11:09:28',
                'updated_at'   => '2025-08-13 08:16:27',
            ],
            [
                'menu_id'      => 1754316593,
                'menu_judul'   => 'Produk',
                'menu_slug'    => 'produk',
                'menu_icon'    => 'package',
                'menu_redirect'=> '-',
                'menu_sort'    => '3',
                'menu_type'    => '2',
                'created_at'   => '2025-08-04 14:09:53',
                'updated_at'   => '2025-08-13 08:16:27',
            ],
            [
                'menu_id'      => 1754316594,
                'menu_judul'   => 'Pengelolaan Cek',
                'menu_slug'    => 'pengelolaan-cek',
                'menu_icon'    => 'file-text',
                'menu_redirect'=> '-',
                'menu_sort'    => '6',
                'menu_type'    => '2',
                'created_at'   => '2025-09-05 14:09:53',
                'updated_at'   => '2025-09-05 08:16:27',
            ],
        ]);
    }
}
