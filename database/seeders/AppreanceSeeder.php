<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppreanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_appreance')->insert([
            'appreance_id'      => 2,
            'user_id'           => '1',
            'appreance_layout'  => 'sidebar-mini',
            'appreance_theme'   => 'light-mode',
            'appreance_menu'    => 'light-menu',
            'appreance_header'  => 'header-light',
            'appreance_sidestyle' => 'default-menu',
        ]);
    }
}
