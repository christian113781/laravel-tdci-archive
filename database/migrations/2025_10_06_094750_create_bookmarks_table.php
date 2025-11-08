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
    Schema::create('bookmarks', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');   
    $table->unsignedBigInteger('archive_id'); 
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('archive_id')->references('id')->on('archives')->onDelete('cascade');
    $table->unique(['user_id', 'archive_id']);
    
});
}

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
