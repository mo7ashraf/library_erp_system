<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('movement_type', 50);
            /*
                opening_balance
                purchase
                purchase_return
                sale
                sale_return
                transfer_in
                transfer_out
                stock_count_increase
                stock_count_decrease
                damaged
                manual_adjustment
            */

            $table->string('direction', 10);
            // in, out

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();

            $table->date('movement_date');
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->decimal('balance_after', 15, 3)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['warehouse_id', 'item_id']);
            $table->index(['movement_type', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};