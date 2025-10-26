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
        Schema::create('tbl_menu', function (Blueprint $table) {
            $table->increments('menu_id');
            $table->string('menu_judul');
            $table->string('menu_slug');
            $table->string('menu_icon');
            $table->string('menu_redirect');
            $table->string('menu_sort');
            $table->string('menu_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_menu');
    }
};
