<?php

namespace App\Filament\Resources\SalesReturns\Tables;

use App\Models\SalesReturn;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalesReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('return_date', 'desc')
            ->columns([
                TextColumn::make('return_number')
                    ->label('رقم المرتجع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('return_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('salesInvoice.invoice_number')
                    ->label('فاتورة البيع')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('refund_type')
                    ->label('رد القيمة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesReturn::REFUND_CASH => 'رد نقدي',
                        SalesReturn::REFUND_CREDIT_BALANCE => 'رصيد للعميل',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SalesReturn::STATUS_DRAFT => 'مسودة',
                        SalesReturn::STATUS_POSTED => 'مرحلة',
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

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        SalesReturn::STATUS_DRAFT => 'مسودة',
                        SalesReturn::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}