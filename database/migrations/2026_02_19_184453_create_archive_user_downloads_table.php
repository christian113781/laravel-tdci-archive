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
        Schema::create('archive_user_downloads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('archive_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('downloads')->default(0); // total downloads for this archive by this user
            $table->timestamps();

            $table->unique(['archive_id', 'user_id']); // ensure one record per user per archive
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_user_downloads');
    }
};
