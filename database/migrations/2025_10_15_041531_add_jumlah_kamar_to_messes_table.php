<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->integer('jumlah_kamar')->after('nama_mess'); // tambahkan setelah kolom nama_mess
        });
    }

    public function down(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->dropColumn('jumlah_kamar');
        });
    }
};
