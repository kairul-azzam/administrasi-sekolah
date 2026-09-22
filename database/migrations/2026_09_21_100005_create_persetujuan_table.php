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
        Schema::create('persetujuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status_lama', [
                'draft',
                'diajukan',
                'diperiksa',
                'dikembalikan',
                'disetujui',
                'ditolak'
            ])->nullable();
            $table->enum('status_baru', [
                'draft',
                'diajukan',
                'diperiksa',
                'dikembalikan',
                'disetujui',
                'ditolak'
            ]);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan');
    }
};
