<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_stock_documents', function (Blueprint $table) {
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

            $table->string('reference_number', 100)->unique();
            $table->date('document_date');

            $table->string('status', 30)->default('draft');
            // draft, posted

            $table->timestamp('posted_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'document_date']);
            $table->index(['status', 'document_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_stock_documents');
    }
};