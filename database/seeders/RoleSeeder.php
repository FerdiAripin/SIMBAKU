<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_role')->insert([
            [
                'role_id'    => 1,
                'role_title' => 'Super Admin',
                'role_slug'  => 'super-admin',
                'role_desc'  => '-',
            ],
            [
                'role_id'    => 2,
                'role_title' => 'Admin',
                'role_slug'  => 'admin',
                'role_desc'  => '-',
            ],
            [
                'role_id'    => 3,
                'role_title' => 'Operator',
                'role_slug'  => 'operator',
                'role_desc'  => '-',
            ],
            [
                'role_id'    => 4,
                'role_title' => 'Manajer',
                'role_slug'  => 'manajer',
                'role_desc'  => null,
            ],
        ]);
    }
}
