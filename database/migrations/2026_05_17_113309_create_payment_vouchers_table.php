<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('cashbox_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('treasury_transaction_id')->nullable()->constrained('treasury_transactions')->nullOnDelete();

            $table->string('voucher_number', 100)->unique();
            $table->date('voucher_date');

            $table->string('payment_channel', 30)->default('cash');
            // cash, bank

            $table->string('party_type', 30)->default('supplier');
            // customer, supplier, other

            $table->string('party_name')->nullable();

            $table->decimal('amount', 15, 2);
            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->timestamp('posted_at')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['voucher_date', 'status']);
            $table->index(['payment_channel']);
            $table->index(['party_type', 'customer_id']);
            $table->index(['party_type', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};