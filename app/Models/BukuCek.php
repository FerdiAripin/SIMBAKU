<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuCek extends Model
{
    use HasFactory;

    protected $table = 'buku_cek';
    protected $primaryKey = 'buku_id';

    protected $fillable = [
        'buku_kode',
        'jenis_buku',
        'jumlah_lembar',
        'nomor_seri_awal',
        'nomor_seri_akhir',
        'status',
        'tanggal_terbit',
        'keterangan',
    ];

    protected $dates = ['tanggal_terbit'];

    public function lembarCek()
    {
        return $this->hasMany(LembarCek::class, 'buku_id', 'buku_id');
    }

    public function lembarTersedia()
    {
        return $this->hasMany(LembarCek::class, 'buku_id', 'buku_id')->where('status', 'tersedia');
    }

    public function lembarTerpakai()
    {
        return $this->hasMany(LembarCek::class, 'buku_id', 'buku_id')->where('status', 'terpakai');
    }

    
    public static function generateKode($jenis)
    {
        $prefix = "BK{$jenis}-" . date('Ymd') . "-";
        $last = self::where('buku_kode', 'like', $prefix . '%')
            ->orderBy('buku_kode', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->buku_kode, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
