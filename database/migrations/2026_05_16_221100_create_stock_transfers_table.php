<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('to_warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('from_branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('to_branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('transfer_number', 100)->unique();
            $table->date('transfer_date');

            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->decimal('total_quantity', 15, 3)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['from_warehouse_id', 'transfer_date']);
            $table->index(['to_warehouse_id', 'transfer_date']);
            $table->index(['status', 'transfer_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};