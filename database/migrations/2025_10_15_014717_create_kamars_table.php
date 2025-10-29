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
    Schema::create('kamars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mess_id')->constrained()->onDelete('cascade');
        $table->string('no_kamar');
        $table->integer('kapasitas');
        $table->enum('status', ['available', 'full'])->default('available');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
