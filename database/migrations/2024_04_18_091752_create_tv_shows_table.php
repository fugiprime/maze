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
        Schema::create('tv_shows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maze_id')->unique();
            $table->string('url');
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('language')->nullable();
            $table->string('status')->nullable();
            $table->unsignedTinyInteger('runtime')->nullable();
            $table->date('premiered')->nullable();
            $table->string('official_site')->nullable();
            $table->decimal('rating', 5, 2)->nullable();
            $table->text('summary')->nullable();
            $table->string('imdb_id')->nullable();
            $table->string('image_url')->nullable();
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
