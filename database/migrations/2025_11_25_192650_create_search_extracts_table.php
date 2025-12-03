<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('search_extracts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('search_id')->constrained('searchs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_paid')->default(false);
            $table->string('cpfcnpj');
            $table->string('status_code');
            $table->string('status_description')->nullable();
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('search_extracts');
    }
};
