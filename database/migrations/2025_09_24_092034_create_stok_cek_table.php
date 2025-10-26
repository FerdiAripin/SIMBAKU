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
        Schema::create('stok_cek', function (Blueprint $table) {
            $table->bigIncrements('stok_id');
            $table->enum('jenis_buku', ['5', '10', '25'])->unique();
            $table->integer('jumlah_buku_tersedia')->default(0);
            $table->integer('jumlah_lembar_tersedia')->default(0);
            $table->integer('jumlah_lembar_terpakai')->default(0);
            $table->integer('jumlah_lembar_rusak')->default(0);
            $table->integer('jumlah_lembar_hilang')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_cek');
    }
};
