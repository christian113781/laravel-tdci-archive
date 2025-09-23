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
        Schema::create('archive_keyword', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archive_id');
            $table->unsignedBigInteger('keyword_id');
            $table->timestamps();

            $table->foreign('archive_id')->references('id')->on('archives')->onDelete('cascade');
            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');

            $table->unique(['archive_id', 'keyword_id']);
            
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_keyword');
    }
};
