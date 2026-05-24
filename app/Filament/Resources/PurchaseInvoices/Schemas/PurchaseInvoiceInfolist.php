<?php

namespace App\Filament\Resources\PurchaseInvoices\Schemas;

use App\Models\PaymentVoucher;
use App\Models\PurchaseInvoice;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseInvoiceInfolist
{
    private static function paidAmount(PurchaseInvoice $record): float
    {
        return (float) PaymentVoucher::query()
            ->where('party_type', PaymentVoucher::PARTY_SUPPLIER)
            ->where('supplier_id', $record->supplier_id)
            ->where('status', PaymentVoucher::STATUS_POSTED)
            ->where('description', 'like', '%' . $record->invoice_number . '%')
            ->sum('amount');
    }

    private static function remainingAmount(PurchaseInvoice $record): float
    {
        return max(0, (float) $record->grand_total - self::paidAmount($record));
    }

    private static function paymentStatus(PurchaseInvoice $record): string
    {
        $paid = self::paidAmount($record);
        $total = (float) $record->grand_total;

        if ($total <= 0) {
            return '-';
        }

        if ($paid <= 0) {
            return 'غير مدفوعة';
        }

        if ($paid >= $total) {
            return 'مدفوعة بالكامل';
        }

        return 'مدفوعة جزئيًا';
    }

    private static function paymentVoucherNumbers(PurchaseInvoice $record): string
    {
        $numbers = PaymentVoucher::query()
            ->where('party_type', PaymentVoucher::PARTY_SUPPLIER)
            ->where('supplier_id', $record->supplier_id)
            ->where('status', PaymentVoucher::STATUS_POSTED)
            ->where('description', 'like', '%' . $record->invoice_number . '%')
            ->orderBy('voucher_date')
            ->pluck('voucher_number')
            ->filter()
            ->implode('، ');

        return $numbers !== '' ? $numbers : '-';
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('invoice_number')
                    ->label('رقم الفاتورة'),

                TextEntry::make('supplier_invoice_number')
                    ->label('رقم فاتورة المورد')
                    ->placeholder('-'),

                TextEntry::make('invoice_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date('Y-m-d')
                    ->placeholder('-'),

                TextEntry::make('supplier.name')
                    ->label('المورد'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('payment_type')
                    ->label('طريقة السداد')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseInvoice::PAYMENT_CASH => 'نقدي',
                        PurchaseInvoice::PAYMENT_CREDIT => 'آجل',
                        PurchaseInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                        default => '-',
                    }),

                TextEntry::make('payment_status')
                    ->label('حالة السداد')
                    ->state(fn (PurchaseInvoice $record): string => self::paymentStatus($record)),

                TextEntry::make('paid_amount')
                    ->label('المبلغ المدفوع')
                    ->state(fn (PurchaseInvoice $record): float => self::paidAmount($record))
                    ->money('EGP'),

                TextEntry::make('remaining_amount')
                    ->label('المبلغ المتبقي')
                    ->state(fn (PurchaseInvoice $record): float => self::remainingAmount($record))
                    ->money('EGP'),

                TextEntry::make('payment_vouchers')
                    ->label('سندات الصرف')
                    ->state(fn (PurchaseInvoice $record): string => self::paymentVoucherNumbers($record))
                    ->columnSpanFull(),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseInvoice::STATUS_DRAFT => 'مسودة',
                        PurchaseInvoice::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    }),

                TextEntry::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('-'),

                TextEntry::make('notes')
                    ->label('ملاحظات')
                    ->placeholder('-')
                    ->columnSpanFull(),

                RepeatableEntry::make('items')
                    ->label('الأصناف')
                    ->columns(5)
                    ->schema([
                        TextEntry::make('item.code')
                            ->label('كود الصنف'),

                        TextEntry::make('item.name')
                            ->label('اسم الصنف'),

                        TextEntry::make('unit.name')
                            ->label('الوحدة')
                            ->placeholder('-'),

                        TextEntry::make('quantity')
                            ->label('الكمية'),

                        TextEntry::make('unit_price')
                            ->label('سعر الشراء')
                            ->money('EGP'),

                        TextEntry::make('discount_percent')
                            ->label('خصم %')
                            ->suffix('%'),

                        TextEntry::make('discount_amount')
                            ->label('قيمة الخصم')
                            ->money('EGP'),

                        TextEntry::make('net_unit_price')
                            ->label('صافي سعر الوحدة')
                            ->money('EGP'),

                        TextEntry::make('line_total')
                            ->label('الإجمالي')
                            ->money('EGP'),
                    ])
                    ->columnSpanFull(),

                TextEntry::make('subtotal')
                    ->label('إجمالي الأصناف')
                    ->money('EGP'),

                TextEntry::make('discount_amount')
                    ->label('خصم الفاتورة')
                    ->money('EGP'),

                TextEntry::make('additional_cost')
                    ->label('مصروفات إضافية')
                    ->money('EGP'),

                TextEntry::make('grand_total')
                    ->label('صافي الفاتورة')
                    ->money('EGP'),

                TextEntry::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}