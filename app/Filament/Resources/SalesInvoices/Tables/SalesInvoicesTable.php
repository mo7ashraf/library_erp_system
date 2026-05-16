<?php

namespace App\Filament\Resources\SalesInvoices\Tables;

use App\Models\SalesInvoice;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalesInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('invoice_date', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoice_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->label('السداد')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesInvoice::PAYMENT_CASH => 'نقدي',
                        SalesInvoice::PAYMENT_CREDIT => 'آجل',
                        SalesInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('price_type')
                    ->label('نوع السعر')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesInvoice::PRICE_STUDENT => 'طالب',
                        SalesInvoice::PRICE_TEACHER => 'مدرس',
                        SalesInvoice::PRICE_REPRESENTATIVE => 'مندوب',
                        SalesInvoice::PRICE_RETAIL => 'قطاعي',
                        SalesInvoice::PRICE_WHOLESALE => 'جملة',
                        default => '-',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesInvoice::STATUS_DRAFT => 'مسودة',
                        SalesInvoice::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('discount_amount')
                    ->label('خصم')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('service_amount')
                    ->label('خدمات')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('commission_amount')
                    ->label('عمولة')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('grand_total')
                    ->label('الصافي')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('customer_id')
                    ->label('العميل')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('warehouse_id')
                    ->label('المخزن')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('payment_type')
                    ->label('طريقة السداد')
                    ->options([
                        SalesInvoice::PAYMENT_CASH => 'نقدي',
                        SalesInvoice::PAYMENT_CREDIT => 'آجل',
                        SalesInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                    ]),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        SalesInvoice::STATUS_DRAFT => 'مسودة',
                        SalesInvoice::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}