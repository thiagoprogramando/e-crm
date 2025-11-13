<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('payment_options', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2);
            $table->decimal('commission_seller', 10, 2)->default(0);
            $table->decimal('commission_parent', 10, 2)->default(0);
            $table->enum('payment_method', ['CREDIT_CARD', 'BOLETO', 'PIX', 'CASH'])->default('PIX');
            $table->integer('payment_installments')->default(1);
            $table->json('payment_splits')->nullable();
            $table->json('payment_settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payment_options');
    }
};
