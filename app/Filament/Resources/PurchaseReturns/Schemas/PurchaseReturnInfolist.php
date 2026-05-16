<?php

namespace App\Filament\Resources\PurchaseReturns\Schemas;

use App\Models\PurchaseReturn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseReturnInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('return_number')
                    ->label('رقم المرتجع'),

                TextEntry::make('return_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('purchaseInvoice.invoice_number')
                    ->label('فاتورة الشراء الأصلية')
                    ->placeholder('-'),

                TextEntry::make('supplier.name')
                    ->label('المورد'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('refund_type')
                    ->label('طريقة رد القيمة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseReturn::REFUND_CASH => 'استرداد نقدي',
                        PurchaseReturn::REFUND_SUPPLIER_BALANCE => 'خصم من رصيد المورد',
                        default => '-',
                    }),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseReturn::STATUS_DRAFT => 'مسودة',
                        PurchaseReturn::STATUS_POSTED => 'مرحلة',
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
                    ->label('خصم المرتجع')
                    ->money('EGP'),

                TextEntry::make('grand_total')
                    ->label('صافي المرتجع')
                    ->money('EGP'),

                TextEntry::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}