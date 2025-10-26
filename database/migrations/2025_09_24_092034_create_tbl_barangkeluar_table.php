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
        Schema::create('tbl_barangkeluar', function (Blueprint $table) {
            $table->increments('bk_id');
            $table->string('bk_kode');
            $table->string('barang_kode');
            $table->string('bk_tanggal');
            $table->string('bk_jumlah');
            $table->text('bk_keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barangkeluar');
    }
};
