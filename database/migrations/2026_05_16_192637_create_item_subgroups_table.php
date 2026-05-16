<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_subgroups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_group_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['item_group_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_subgroups');
    }
};