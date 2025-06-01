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
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->foreignId("kelas_id")->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId("jurusan_id")->nullable()->constrained("jurusan")->onDelete('cascade')->onUpdate('cascade');
            $table->enum('gender', ['pria', 'wanita']);
            $table->string('no_telp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
