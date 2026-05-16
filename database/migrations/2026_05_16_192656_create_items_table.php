<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('item_subgroup_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('base_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('middle_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('large_unit_id')->nullable()->constrained('units')->nullOnDelete();

            $table->string('code', 50)->unique();
            $table->string('origin_code', 100)->nullable();
            $table->string('barcode', 100)->nullable()->unique();

            $table->string('name');
            $table->string('source')->nullable();
            $table->string('publisher')->nullable();

            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('first_discount_percent', 8, 2)->default(0);
            $table->decimal('second_discount_percent', 8, 2)->default(0);
            $table->decimal('net_purchase_price', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('profit_margin_percent', 8, 2)->default(0);

            $table->decimal('student_price', 15, 2)->default(0);
            $table->decimal('teacher_price', 15, 2)->default(0);
            $table->decimal('representative_price', 15, 2)->default(0);
            $table->decimal('retail_price', 15, 2)->default(0);
            $table->decimal('wholesale_price', 15, 2)->default(0);

            $table->decimal('teacher_discount_percent', 8, 2)->default(0);
            $table->decimal('representative_discount_percent', 8, 2)->default(0);
            $table->decimal('return_percent', 8, 2)->default(0);

            $table->decimal('max_stock', 15, 3)->default(0);
            $table->decimal('min_stock', 15, 3)->default(0);
            $table->decimal('reorder_level', 15, 3)->default(0);

            $table->unsignedInteger('units_per_middle')->nullable();
            $table->unsignedInteger('units_per_large')->nullable();

            $table->string('image_path')->nullable();
            $table->text('details')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('continue_balance')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['name', 'code', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};