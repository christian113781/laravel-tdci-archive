<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('archive_code')->unique();
            $table->string('title');
            $table->text('subject');
            $table->year('year');
            $table->unsignedBigInteger('program_id'); 
            $table->string('authors')->nullable();
            $table->enum('category', ['A', 'B'])->default('A'); // A = general, B = restricted
            $table->string('file_path')->nullable();
            $table->string('thesis_file')->nullable();
            $table->string('tables_file')->nullable();
            $table->string('figures_file')->nullable();
            $table->string('recommendation_file')->nullable();
            $table->string('citation')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('views')->default(0); // track number of views
            $table->enum('status', ['Unpublish', 'Publish'])->default('Unpublish');
            $table->timestamps();

            // Foreign keys
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
