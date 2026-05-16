<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();

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

            $table->string('invoice_number', 100)->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            $table->string('payment_type', 30)->default('cash');
            // cash, credit, partial

            $table->string('price_type', 30)->default('student');
            // student, teacher, representative, retail, wholesale

            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('service_amount', 15, 2)->default(0);
            $table->decimal('commission_percent', 8, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->timestamp('posted_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'invoice_date']);
            $table->index(['warehouse_id', 'invoice_date']);
            $table->index(['status', 'invoice_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};