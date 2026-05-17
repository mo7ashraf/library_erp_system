<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_count_document_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_count_document_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->decimal('system_quantity', 15, 3)->default(0);
            $table->decimal('actual_quantity', 15, 3)->default(0);
            $table->decimal('difference_quantity', 15, 3)->default(0);

            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('difference_cost', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_count_document_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_count_document_items');
    }
};