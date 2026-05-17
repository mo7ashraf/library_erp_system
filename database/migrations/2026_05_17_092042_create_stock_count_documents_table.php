<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_count_documents', function (Blueprint $table) {
            $table->id();

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

            $table->string('count_number', 100)->unique();
            $table->date('count_date');

            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->decimal('total_increase_quantity', 15, 3)->default(0);
            $table->decimal('total_decrease_quantity', 15, 3)->default(0);
            $table->decimal('total_difference_cost', 15, 2)->default(0);

            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'count_date']);
            $table->index(['status', 'count_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_count_documents');
    }
};