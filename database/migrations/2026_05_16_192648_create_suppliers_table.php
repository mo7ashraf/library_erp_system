<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            $table->string('code', 50)->unique();
            $table->string('name');

            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('governorate')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->string('balance_type', 20)->default('credit');
            // debit = عليه, credit = له

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['name', 'phone', 'mobile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};