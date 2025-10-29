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
        Schema::table('penghunis', function (Blueprint $table) {
            $table->enum('status', allowed: ['aktif', 'keluar'])->default('aktif')->after('no_telp');
        });
    }

    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
