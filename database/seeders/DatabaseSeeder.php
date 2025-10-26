<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            KategoriSeeder::class,
            BarangSeeder::class,
            BarangMasukSeeder::class,
            BarangKeluarSeeder::class,
            AppreanceSeeder::class,
            MenuSeeder::class,
            SubmenuSeeder::class,
            TblAksesSeeder::class,
            WebSeeder::class,
        ]);
    }
}
