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
        Schema::create('buku_cek', function (Blueprint $table) {
            $table->bigIncrements('buku_id');
            $table->string('buku_kode', 20)->unique();
            $table->enum('jenis_buku', ['5', '10', '25'])->comment('Jumlah lembar per buku');
            $table->integer('jumlah_lembar');
            $table->string('nomor_seri_awal', 20);
            $table->string('nomor_seri_akhir', 20);
            $table->string('kode_huruf', 3)->nullable();
            $table->string('kode_angka', 2)->nullable();
            $table->enum('status', ['aktif', 'habis', 'non_aktif'])->default('aktif');
            $table->date('tanggal_terbit');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['status', 'jenis_buku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_cek');
    }
};
