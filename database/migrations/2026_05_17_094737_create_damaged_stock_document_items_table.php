<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_stock_document_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('damaged_stock_document_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['damaged_stock_document_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_stock_document_items');
    }
};