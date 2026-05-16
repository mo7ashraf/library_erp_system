<?php

namespace App\Filament\Resources\PurchaseInvoices\Schemas;

use App\Models\PurchaseInvoice;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseInvoiceInfolist
{
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