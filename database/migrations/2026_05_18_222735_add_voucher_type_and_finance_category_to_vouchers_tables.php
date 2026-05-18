<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipt_vouchers', function (Blueprint $table) {
            $table->string('voucher_type', 50)
                ->default('customer_collection')
                ->after('payment_channel');

            $table->foreignId('finance_category_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('finance_categories')
                ->nullOnDelete();

            $table->index(['voucher_type', 'voucher_date']);
            $table->index(['finance_category_id', 'voucher_date']);
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->string('voucher_type', 50)
                ->default('supplier_payment')
                ->after('payment_channel');

            $table->foreignId('finance_category_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('finance_categories')
                ->nullOnDelete();

            $table->index(['voucher_type', 'voucher_date']);
            $table->index(['finance_category_id', 'voucher_date']);
        });
    }

    public function down(): void
    {
        Schema::table('receipt_vouchers', function (Blueprint $table) {
            $table->dropForeign(['finance_category_id']);
            $table->dropIndex(['voucher_type', 'voucher_date']);
            $table->dropIndex(['finance_category_id', 'voucher_date']);
            $table->dropColumn(['voucher_type', 'finance_category_id']);
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropForeign(['finance_category_id']);
            $table->dropIndex(['voucher_type', 'voucher_date']);
            $table->dropIndex(['finance_category_id', 'voucher_date']);
            $table->dropColumn(['voucher_type', 'finance_category_id']);
        });
    }
};