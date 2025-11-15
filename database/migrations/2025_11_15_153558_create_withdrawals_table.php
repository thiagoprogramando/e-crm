<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_name');
            $table->string('payment_document');
            $table->string('payment_key');
            $table->string('payment_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->string('description')->nullable();
            $table->date('confirmed_at')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('withdrawals');
    }
};
