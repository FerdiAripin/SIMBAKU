<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    use HasFactory;
    protected $table = "tbl_kategori";
    protected $primaryKey = 'kategori_id';
    protected $fillable = [
        'kategori_nama',
        'kategori_slug',
        'kategori_ket'
    ]; 
}
