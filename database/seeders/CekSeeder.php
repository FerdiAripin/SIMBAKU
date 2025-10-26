<?php
// database/seeders/CekSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BukuCek;
use App\Models\LembarCek;
use App\Models\StokCek;
use Illuminate\Support\Facades\DB;

class CekSeeder extends Seeder
{
    public function run()
    {
        try {
            // Hapus data lama jika ada
            LembarCek::query()->delete();
            BukuCek::query()->delete();
            StokCek::query()->delete();

            // Insert data stok awal
            $stokData = [
                ['jenis_buku' => '5', 'created_at' => now(), 'updated_at' => now()],
                ['jenis_buku' => '10', 'created_at' => now(), 'updated_at' => now()],
                ['jenis_buku' => '25', 'created_at' => now(), 'updated_at' => now()],
            ];
            StokCek::insert($stokData);

            // Data contoh buku cek
            $bukuCekData = [
                [
                    'jenis_buku' => '5',
                    'jumlah_buku' => 3,
                    'nomor_seri_awal' => 1000001,
                ],
                [
                    'jenis_buku' => '10',
                    'jumlah_buku' => 2,
                    'nomor_seri_awal' => 2000001,
                ],
                [
                    'jenis_buku' => '25',
                    'jumlah_buku' => 1,
                    'nomor_seri_awal' => 3000001,
                ]
            ];

            foreach ($bukuCekData as $data) {
                $this->createBukuCek($data['jenis_buku'], $data['jumlah_buku'], $data['nomor_seri_awal']);
            }

            // Update stok untuk semua jenis
            foreach (['5', '10', '25'] as $jenis) {
                $this->updateStokManual($jenis);
            }

            // Buat beberapa contoh penggunaan cek
            $this->createSampleUsage();

            // Update stok lagi setelah penggunaan
            foreach (['5', '10', '25'] as $jenis) {
                $this->updateStokManual($jenis);
            }

            $this->command->info('✅ Seeder CekSeeder berhasil dijalankan!');

        } catch (\Exception $e) {
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createBukuCek($jenisBuku, $jumlahBuku, $nomorSeriAwal)
    {
        $jumlahLembarPerBuku = (int) $jenisBuku;

        for ($i = 0; $i < $jumlahBuku; $i++) {
            // Generate kode buku sederhana karena method generateKode mungkin belum ada
            $bukuKode = "BK{$jenisBuku}-" . date('Ymd') . "-" . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $currentSeriAwal = $nomorSeriAwal + ($i * $jumlahLembarPerBuku);
            $currentSeriAkhir = $currentSeriAwal + $jumlahLembarPerBuku - 1;

            // Buat buku cek
            $bukuCek = BukuCek::create([
                'buku_kode' => $bukuKode,
                'jenis_buku' => $jenisBuku,
                'jumlah_lembar' => $jumlahLembarPerBuku,
                'nomor_seri_awal' => str_pad($currentSeriAwal, 10, '0', STR_PAD_LEFT),
                'nomor_seri_akhir' => str_pad($currentSeriAkhir, 10, '0', STR_PAD_LEFT),
                'status' => 'aktif',
                'tanggal_terbit' => now()->subDays(rand(1, 30)),
                'keterangan' => "Buku cek {$jenisBuku} lembar - batch " . ($i + 1)
            ]);

            // Buat lembar cek
            $lembarCekData = [];
            for ($j = 0; $j < $jumlahLembarPerBuku; $j++) {
                $nomorSeri = str_pad($currentSeriAwal + $j, 10, '0', STR_PAD_LEFT);

                $lembarCekData[] = [
                    'buku_id' => $bukuCek->buku_id,
                    'nomor_seri' => $nomorSeri,
                    'status' => 'tersedia',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Bulk insert lembar cek
            LembarCek::insert($lembarCekData);

            $this->command->info("✓ Dibuat buku cek {$bukuKode} dengan {$jumlahLembarPerBuku} lembar");
        }
    }

    private function createSampleUsage()
    {
        // Daftar contoh penerima dan keperluan
        $contohPenerima = [
            'PT. Sumber Makmur',
            'CV. Jaya Abadi',
            'Toko Berkah',
            'UD. Maju Jaya',
            'PT. Sukses Mandiri',
            'John Doe',
            'Jane Smith',
            'Ahmad Wijaya',
            'Siti Nurhaliza',
            'Budi Santoso'
        ];

        $contohKeperluan = [
            'Pembayaran supplier',
            'Gaji karyawan',
            'Biaya operasional',
            'Pembelian bahan baku',
            'Biaya transportasi',
            'Pembayaran listrik',
            'Sewa gedung',
            'Biaya maintenance',
            'Pembayaran vendor',
            'Biaya konsultasi'
        ];

        // Ambil beberapa lembar cek untuk dijadikan contoh penggunaan
        $lembarTersedia = LembarCek::where('status', 'tersedia')->take(15)->get();

        foreach ($lembarTersedia as $index => $lembar) {
            if ($index < 8) { // 8 lembar digunakan
                $lembar->update([
                    'status' => 'terpakai',
                    'nominal' => rand(100000, 5000000),
                    'penerima' => $contohPenerima[array_rand($contohPenerima)],
                    'tanggal_pakai' => now()->subDays(rand(1, 30)),
                    'keperluan' => $contohKeperluan[array_rand($contohKeperluan)],
                    'user_id' => 1, // Assuming user ID 1 exists
                    'keterangan' => 'Data contoh dari seeder'
                ]);

                $this->command->info("✓ Cek {$lembar->nomor_seri} digunakan");

            } elseif ($index < 10) { // 2 lembar rusak
                $lembar->update([
                    'status' => 'rusak',
                    'keterangan' => 'Rusak saat pencetakan - data contoh',
                    'user_id' => 1
                ]);

                $this->command->info("✓ Cek {$lembar->nomor_seri} ditandai rusak");

            } elseif ($index < 11) { // 1 lembar hilang
                $lembar->update([
                    'status' => 'hilang',
                    'keterangan' => 'Hilang saat pengiriman - data contoh',
                    'user_id' => 1
                ]);

                $this->command->info("✓ Cek {$lembar->nomor_seri} ditandai hilang");
            }
        }
    }

    // Manual update stok untuk menghindari error transaksi
    private function updateStokManual($jenis)
    {
        $bukuTersedia = BukuCek::where('jenis_buku', $jenis)
                               ->where('status', 'aktif')
                               ->count();

        $lembarTersedia = LembarCek::whereHas('bukuCek', function($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'tersedia')->count();

        $lembarTerpakai = LembarCek::whereHas('bukuCek', function($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'terpakai')->count();

        $lembarRusak = LembarCek::whereHas('bukuCek', function($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'rusak')->count();

        $lembarHilang = LembarCek::whereHas('bukuCek', function($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'hilang')->count();

        StokCek::updateOrCreate(
            ['jenis_buku' => $jenis],
            [
                'jumlah_buku_tersedia' => $bukuTersedia,
                'jumlah_lembar_tersedia' => $lembarTersedia,
                'jumlah_lembar_terpakai' => $lembarTerpakai,
                'jumlah_lembar_rusak' => $lembarRusak,
                'jumlah_lembar_hilang' => $lembarHilang
            ]
        );

        $this->command->info("✓ Stok cek {$jenis} lembar diupdate: {$lembarTersedia} tersedia, {$lembarTerpakai} terpakai");
    }
}
