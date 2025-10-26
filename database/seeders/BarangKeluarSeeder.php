<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangKeluarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_barangkeluar')->insert([
            'bk_id'        => 4,
            'bk_kode'      => 'BK-1754622580775',
            'barang_kode'  => 'BRG-1754402351047',
            'bk_tanggal'   => '2025-08-09',
            'bk_jumlah'    => '5',
            'bk_keterangan'=> 'test',
        ]);
    }
}
