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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('alat_id')->constrained('alat')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('peminjam_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
