<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->date('due_date')->default(now()->addDays(7));
            $table->date('payment_date')->nullable();
            $table->enum('payment_status', ['PENDING', 'PAID', 'OVERDUE'])->default('PENDING');
            $table->string('payment_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};
