<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_item_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('average_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->timestamps();

            $table->unique(['warehouse_id', 'item_id']);
            $table->index(['item_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_item_balances');
    }
};