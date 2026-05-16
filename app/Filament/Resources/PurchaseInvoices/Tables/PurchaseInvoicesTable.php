<?php

namespace App\Filament\Resources\PurchaseInvoices\Tables;

use App\Models\PurchaseInvoice;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseInvoicesTable
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

                TextColumn::make('supplier_invoice_number')
                    ->label('رقم فاتورة المورد')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('invoice_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->label('السداد')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseInvoice::PAYMENT_CASH => 'نقدي',
                        PurchaseInvoice::PAYMENT_CREDIT => 'آجل',
                        PurchaseInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseInvoice::STATUS_DRAFT => 'مسودة',
                        PurchaseInvoice::STATUS_POSTED => 'مرحلة',
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

                TextColumn::make('additional_cost')
                    ->label('إضافات')
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
                SelectFilter::make('supplier_id')
                    ->label('المورد')
                    ->relationship('supplier', 'name')
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
                        PurchaseInvoice::PAYMENT_CASH => 'نقدي',
                        PurchaseInvoice::PAYMENT_CREDIT => 'آجل',
                        PurchaseInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                    ]),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        PurchaseInvoice::STATUS_DRAFT => 'مسودة',
                        PurchaseInvoice::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}