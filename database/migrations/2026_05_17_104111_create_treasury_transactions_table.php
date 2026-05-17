<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('cashbox_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('transaction_number', 100)->unique();
            $table->date('transaction_date');

            $table->string('payment_channel', 30);
            // cash, bank

            $table->string('direction', 10);
            // in, out

            $table->string('transaction_type', 50);
            /*
                opening_balance
                customer_receipt
                supplier_payment
                expense
                income
                sales_invoice
                purchase_invoice
                sales_return
                purchase_return
                manual_adjustment
            */

            $table->string('party_type', 50)->nullable();
            // customer, supplier, other

            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_name')->nullable();

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();

            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2)->default(0);

            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['cashbox_id', 'transaction_date']);
            $table->index(['bank_account_id', 'transaction_date']);
            $table->index(['direction', 'transaction_type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['party_type', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
    }
};