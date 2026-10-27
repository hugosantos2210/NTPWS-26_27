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
        Schema::create('breed_cache', function (Blueprint $table) {
            $table->id();
            $table->string('species', 10);
            $table->string('breed_name');
            $table->json('data_json')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['species', 'breed_name']);
            $table->index('fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breed_cache');
    }
};
