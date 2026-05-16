<?php

namespace App\Filament\Resources\SalesInvoices\Schemas;

use App\Models\SalesInvoice;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalesInvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('invoice_number')
                    ->label('رقم الفاتورة'),

                TextEntry::make('invoice_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date('Y-m-d')
                    ->placeholder('-'),

                TextEntry::make('customer.name')
                    ->label('العميل'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('payment_type')
                    ->label('طريقة السداد')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesInvoice::PAYMENT_CASH => 'نقدي',
                        SalesInvoice::PAYMENT_CREDIT => 'آجل',
                        SalesInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                        default => '-',
                    }),

                TextEntry::make('price_type')
                    ->label('نوع السعر')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesInvoice::PRICE_STUDENT => 'سعر طالب',
                        SalesInvoice::PRICE_TEACHER => 'سعر مدرس',
                        SalesInvoice::PRICE_REPRESENTATIVE => 'سعر مندوب',
                        SalesInvoice::PRICE_RETAIL => 'سعر قطاعي',
                        SalesInvoice::PRICE_WHOLESALE => 'سعر جملة',
                        default => '-',
                    }),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesInvoice::STATUS_DRAFT => 'مسودة',
                        SalesInvoice::STATUS_POSTED => 'مرحلة',
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
                            ->label('سعر البيع')
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

                TextEntry::make('service_amount')
                    ->label('خدمات')
                    ->money('EGP'),

                TextEntry::make('commission_amount')
                    ->label('قيمة العمولة')
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