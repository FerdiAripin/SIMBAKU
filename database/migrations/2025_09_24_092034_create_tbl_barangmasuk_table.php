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
        Schema::create('tbl_barangmasuk', function (Blueprint $table) {
            $table->increments('bm_id');
            $table->string('bm_kode');
            $table->string('barang_kode');
            $table->string('bm_tanggal');
            $table->string('bm_jumlah');
            $table->text('bm_keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barangmasuk');
    }
};
