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
        Schema::create('asistencia_miembro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asistencia_id')->constrained()->onDelete('cascade');
            $table->foreignId('miembro_id')->constrained()->onDelete('cascade');
            $table->boolean('attended')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_miembro');
    }
};
