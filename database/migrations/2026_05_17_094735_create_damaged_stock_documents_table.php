<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_stock_documents', function (Blueprint $table) {
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

            $table->string('document_number', 100)->unique();
            $table->date('document_date');

            $table->string('reason_type', 50)->default('damaged');
            // damaged, lost, expired, other

            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->decimal('total_quantity', 15, 3)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'document_date']);
            $table->index(['status', 'document_date']);
            $table->index(['reason_type', 'document_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_stock_documents');
    }
};