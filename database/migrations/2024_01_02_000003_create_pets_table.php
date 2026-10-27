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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('species', ['dog', 'cat', 'other']);
            $table->string('breed')->nullable();
            $table->unsignedTinyInteger('age_years')->default(0);
            $table->unsignedTinyInteger('age_months')->default(0);
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->enum('size', ['small', 'medium', 'large', 'extra_large'])->default('medium');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['available', 'adopted', 'pending'])->default('available');
            $table->timestamps();

            $table->index('species');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
