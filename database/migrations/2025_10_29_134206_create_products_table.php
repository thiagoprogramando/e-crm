<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('max_value', 10, 2)->nullable();
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('cost_value', 10, 2)->nullable();
            $table->decimal('fees_value', 10, 2)->nullable();
            $table->decimal('cashback_value', 10, 2)->default(0);
            $table->decimal('cashback_percentage', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->enum('type', ['product', 'subscription', 'service'])->default('product');
            $table->enum('time', ['monthly', 'semi-annually', 'yearly', 'lifetime'])->default('monthly');
            $table->enum('access', ['master', 'admin', 'collaborator', 'user'])->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
