<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('searchs', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('title');
            $table->text('content')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('addition', 10, 2)->default(0);
            $table->longText('api_method')->nullable();
            $table->longText('api_url')->nullable();
            $table->longText('api_token')->nullable();
            $table->longText('api_code')->nullable();
            $table->longText('api_version')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('searchs');
    }
};
