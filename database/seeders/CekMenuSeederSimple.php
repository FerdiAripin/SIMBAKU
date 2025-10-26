<?php
// database/seeders/CekMenuSeederSimple.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CekMenuSeederSimple extends Seeder
{
    public function run()
    {
        try {
            // Lihat struktur tabel dulu
            $menuColumns = collect(DB::select('DESCRIBE tbl_menu'))->pluck('Field')->toArray();
            $submenuColumns = collect(DB::select('DESCRIBE tbl_submenu'))->pluck('Field')->toArray();
            $aksesColumns = collect(DB::select('DESCRIBE tbl_akses'))->pluck('Field')->toArray();

            $this->info("Kolom tbl_menu: " . implode(', ', $menuColumns));
            $this->info("Kolom tbl_submenu: " . implode(', ', $submenuColumns));
            $this->info("Kolom tbl_akses: " . implode(', ', $aksesColumns));

            // Lihat contoh data yang ada
            $existingMenu = DB::table('tbl_menu')->first();
            $existingSubmenu = DB::table('tbl_submenu')->first();
            $existingAkses = DB::table('tbl_akses')->first();

            $this->info("Contoh data menu:");
            $this->info(json_encode($existingMenu, JSON_PRETTY_PRINT));

            $this->info("Contoh data submenu:");
            $this->info(json_encode($existingSubmenu, JSON_PRETTY_PRINT));

            $this->info("Contoh data akses:");
            $this->info(json_encode($existingAkses, JSON_PRETTY_PRINT));

            // Buat data menu berdasarkan struktur yang ada
            $menuData = [
                'menu_judul' => 'Pengelolaan Cek',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Tambahkan field opsional jika ada
            if (in_array('menu_icon', $menuColumns)) {
                $menuData['menu_icon'] = 'file-text';
            }
            if (in_array('menu_redirect', $menuColumns)) {
                $menuData['menu_redirect'] = '#';
            }
            if (in_array('menu_type', $menuColumns)) {
                $menuData['menu_type'] = 2;
            }
            if (in_array('menu_sort', $menuColumns)) {
                $menuData['menu_sort'] = 15;
            }
            if (in_array('menu_slug', $menuColumns)) {
                $menuData['menu_slug'] = 'pengelolaan-cek';
            }

            // Insert menu
            $menuId = DB::table('tbl_menu')->insertGetId($menuData);
            $this->info("✅ Menu berhasil dibuat dengan ID: {$menuId}");

            // Buat submenu
            $submenus = [
                [
                    'menu_id' => $menuId,
                    'submenu_judul' => 'Data Buku Cek',
                    'submenu_redirect' => '/cek',
                    'submenu_sort' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'menu_id' => $menuId,
                    'submenu_judul' => 'Stok Cek',
                    'submenu_redirect' => '/cek/stok/dashboard',
                    'submenu_sort' => 2,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'menu_id' => $menuId,
                    'submenu_judul' => 'Laporan Cek',
                    'submenu_redirect' => '/cek/laporan',
                    'submenu_sort' => 3,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];

            // Tambahkan slug jika ada
            if (in_array('submenu_slug', $submenuColumns)) {
                $submenus[0]['submenu_slug'] = 'data-buku-cek';
                $submenus[1]['submenu_slug'] = 'stok-cek';
                $submenus[2]['submenu_slug'] = 'laporan-cek';
            }

            $submenuIds = [];
            foreach ($submenus as $submenu) {
                $submenuId = DB::table('tbl_submenu')->insertGetId($submenu);
                $submenuIds[] = $submenuId;
                $this->info("✅ Submenu '{$submenu['submenu_judul']}' berhasil dibuat dengan ID: {$submenuId}");
            }

            // Berikan akses ke role admin (diasumsikan role_id = 1)
            $roleId = 2; // Sesuaikan dengan role admin Anda

            // Akses untuk menu utama
            $aksesMenuData = [
                'role_id' => $roleId,
                'menu_id' => $menuId,
                'akses_type' => 'view',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Tambahkan field yang diperlukan
            if (in_array('submenu_id', $aksesColumns)) {
                $aksesMenuData['submenu_id'] = null;
            }
            if (in_array('othermenu_id', $aksesColumns)) {
                $aksesMenuData['othermenu_id'] = null;
            }

            DB::table('tbl_akses')->insert($aksesMenuData);
            $this->info("✅ Akses menu untuk role {$roleId} berhasil dibuat");

            // Akses untuk submenu
            foreach ($submenuIds as $index => $submenuId) {
                $aksesSubmenuData = [
                    'role_id' => $roleId,
                    'submenu_id' => $submenuId,
                    'akses_type' => 'view',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Tambahkan field yang diperlukan
                if (in_array('menu_id', $aksesColumns)) {
                    $aksesSubmenuData['menu_id'] = null;
                }
                if (in_array('othermenu_id', $aksesColumns)) {
                    $aksesSubmenuData['othermenu_id'] = null;
                }

                DB::table('tbl_akses')->insert($aksesSubmenuData);

                // Tambah akses create, update, delete untuk submenu pertama (Data Buku Cek)
                if ($index == 0) {
                    foreach (['create', 'update', 'delete'] as $type) {
                        $aksesSubmenuData['akses_type'] = $type;
                        $aksesSubmenuData['created_at'] = now();
                        $aksesSubmenuData['updated_at'] = now();
                        DB::table('tbl_akses')->insert($aksesSubmenuData);
                    }
                }

                $this->info("✅ Akses submenu ID {$submenuId} untuk role {$roleId} berhasil dibuat");
            }

            $this->info("🎉 Setup menu Pengelolaan Cek berhasil!");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function info($message)
    {
        $this->command->info($message);
    }

    private function error($message)
    {
        $this->command->error($message);
    }
}
