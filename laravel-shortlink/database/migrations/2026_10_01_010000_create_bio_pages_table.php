<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bio_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug', 30)->unique();
            $table->string('title', 100);
            $table->json('links');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bio_pages');
    }
};
