<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LembarCek extends Model
{
    use HasFactory;

    protected $table = 'lembar_cek';
    protected $primaryKey = 'lembar_id';

    protected $fillable = [
        'buku_id',
        'nomor_seri',
        'status',
        'nominal',
        'penerima',
        'tanggal_pakai',
        'keperluan',
        'user_id',
        'keterangan'
    ];

    protected $dates = ['tanggal_pakai'];

    public function bukuCek()
    {
        return $this->belongsTo(BukuCek::class, 'buku_id', 'buku_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
