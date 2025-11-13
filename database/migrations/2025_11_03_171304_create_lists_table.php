<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('status_serasa', ['pending', 'completed'])->default('pending');
            $table->enum('status_boa_vista', ['pending', 'completed'])->default('pending');
            $table->enum('status_spc', ['pending', 'completed'])->default('pending');
            $table->enum('status_ceprot', ['pending', 'completed'])->default('pending');
            $table->enum('status_bacen', ['pending', 'completed'])->default('pending');
            $table->enum('status_rating', ['pending', 'completed'])->default('pending');
            $table->enum('status_score', ['pending', 'completed'])->default('pending');
            $table->dateTime('date_start')->default(now());
            $table->dateTime('date_end')->default(now()->addDays(7));
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lists');
    }
};
