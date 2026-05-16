<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_invoice_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('return_number', 100)->unique();
            $table->date('return_date');

            $table->string('refund_type', 30)->default('cash');
            // cash, credit_balance

            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->timestamp('posted_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'return_date']);
            $table->index(['warehouse_id', 'return_date']);
            $table->index(['sales_invoice_id']);
            $table->index(['status', 'return_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};