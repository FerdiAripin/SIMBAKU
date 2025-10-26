<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_web')->insert([
            'web_id'       => 1,
            'web_nama'     => 'SIMBAKU',
            'web_logo'     => 'bjb.png',
            'web_deskripsi'=> 'Mengelola Buku Cek, ATM & Tabungan',
        ]);
    }
}
