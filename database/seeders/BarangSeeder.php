<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_barang')->insert([
            [
                'barang_id'     => 13,
                'kategori_id'   => '1',
                'barang_kode'   => 'BRG-1754402351047',
                'barang_nama'   => 'Produk Baru 1',
                'barang_slug'   => 'produk-baru-1',
                'barang_type'   => 'baru',
                'barang_stok'   => '0',
                'barang_gambar' => 'image.png',
            ],
            [
                'barang_id'     => 15,
                'kategori_id'   => '1',
                'barang_kode'   => 'BRG-1754402702916',
                'barang_nama'   => 'Produk Lama 1',
                'barang_slug'   => 'produk-lama-1',
                'barang_type'   => 'lama',
                'barang_stok'   => '0',
                'barang_gambar' => 'image.png',
            ],
        ]);
    }
}
