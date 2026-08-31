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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->unsignedSmallInteger('durasi');
            $table->decimal('rating', 3, 1);
            $table->text('deskripsi');
            $table->date('tanggal_rilis');
            $table->string('poster')->nullable();
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->string('sutradara');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
