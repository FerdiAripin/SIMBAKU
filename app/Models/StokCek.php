<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokCek extends Model
{
    use HasFactory;

    protected $table = 'stok_cek';
    protected $primaryKey = 'stok_id';

    protected $fillable = [
        'jenis_buku',
        'jumlah_buku_tersedia',
        'jumlah_lembar_tersedia',
        'jumlah_lembar_terpakai',
        'jumlah_lembar_rusak',
        'jumlah_lembar_hilang'
    ];

    public static function updateStok($jenis)
    {
        $bukuTersedia = BukuCek::where('jenis_buku', $jenis)
            ->where('status', 'aktif')
            ->count();

        $lembarTersedia = LembarCek::whereHas('bukuCek', function ($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'tersedia')->count();

        $lembarTerpakai = LembarCek::whereHas('bukuCek', function ($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'terpakai')->count();

        $lembarRusak = LembarCek::whereHas('bukuCek', function ($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'rusak')->count();

        $lembarHilang = LembarCek::whereHas('bukuCek', function ($q) use ($jenis) {
            $q->where('jenis_buku', $jenis);
        })->where('status', 'hilang')->count();

        self::updateOrCreate(
            ['jenis_buku' => $jenis],
            [
                'jumlah_buku_tersedia' => $bukuTersedia,
                'jumlah_lembar_tersedia' => $lembarTersedia,
                'jumlah_lembar_terpakai' => $lembarTerpakai,
                'jumlah_lembar_rusak' => $lembarRusak,
                'jumlah_lembar_hilang' => $lembarHilang
            ]
        );
    }
}
