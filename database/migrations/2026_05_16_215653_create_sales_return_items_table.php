<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sales_invoice_item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_price', 15, 2)->default(0);

            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);

            $table->decimal('net_unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sales_return_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};