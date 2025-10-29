<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Ubah kolom sementara ke string (menghapus enum lama)
        Schema::table('penghunis', function (Blueprint $table) {
            $table->string('status')->default('onsite')->change();
        });

        // 2️⃣ Perbaiki data lama supaya sesuai format baru
        DB::table('penghunis')
            ->whereNotIn('status', ['onsite', 'offsite'])
            ->orWhereNull('status')
            ->update(['status' => 'onsite']);

        // 3️⃣ Ubah kembali jadi enum baru
        Schema::table('penghunis', function (Blueprint $table) {
            $table->enum('status', ['onsite', 'offsite'])
                  ->default('onsite')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'keluar'])
                  ->default('aktif')
                  ->change();
        });
    }
};
    