<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_barang', function (Blueprint $table) {
            $table->integer('barang_id', true);
            $table->string('kategori_id')->nullable();
            $table->string('barang_kode');
            $table->string('barang_nama');
            $table->string('barang_slug')->nullable();
            $table->string('barang_type')->nullable();
            $table->string('barang_stok');
            $table->string('barang_gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barang');
    }
};
