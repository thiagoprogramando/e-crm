<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('payment_option_id')->constrained('payment_options')->nullOnDelete();
            $table->foreignId('list_id')->nullable()->constrained('lists')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_cpfcnpj');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('payment_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->date('payment_due_date')->default(now()->addDays(2));
            $table->date('payment_date')->nullable();
            $table->enum('payment_status', ['PENDING', 'PAID', 'CANCELED', 'REFUNDED', 'FAILED'])->default('PENDING');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales');
    }
};
