<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    use HasFactory;
    protected $table = "tbl_barang";
    protected $primaryKey = 'barang_id';
    protected $fillable = [
        'kategori_id',
        'barang_kode',
        'barang_nama',
        'barang_slug',
        'barang_stok',
        'barang_type',
        'barang_gambar',
    ]; 
}
