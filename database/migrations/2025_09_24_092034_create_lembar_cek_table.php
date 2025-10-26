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
        Schema::create('lembar_cek', function (Blueprint $table) {
            $table->bigIncrements('lembar_id');
            $table->unsignedBigInteger('buku_id')->index('lembar_cek_buku_id_foreign');
            $table->string('nomor_seri', 20)->index();
            $table->enum('status', ['tersedia', 'terpakai', 'rusak', 'hilang'])->default('tersedia');
            $table->decimal('nominal', 15)->nullable();
            $table->string('penerima', 100)->nullable();
            $table->date('tanggal_pakai')->nullable();
            $table->string('keperluan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['nomor_seri']);
            $table->index(['status', 'buku_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembar_cek');
    }
};
