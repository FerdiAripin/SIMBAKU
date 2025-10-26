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
        Schema::table('lembar_cek', function (Blueprint $table) {
            $table->foreign(['buku_id'])->references(['buku_id'])->on('buku_cek')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembar_cek', function (Blueprint $table) {
            $table->dropForeign('lembar_cek_buku_id_foreign');
        });
    }
};
