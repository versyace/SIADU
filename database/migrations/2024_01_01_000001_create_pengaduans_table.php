<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aduan')->unique()->nullable();
            $table->string('nama');
            $table->string('kelas');
            $table->string('lokasi');
            $table->string('fasilitas');
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->enum('status', ['dilaporkan', 'proses', 'selesai'])->default('dilaporkan');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};