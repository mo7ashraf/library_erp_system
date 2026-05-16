<?php

namespace App\Filament\Resources\PurchaseReturns\Tables;

use App\Models\PurchaseReturn;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseReturnsTable
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

                TextColumn::make('purchaseInvoice.invoice_number')
                    ->label('فاتورة الشراء')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('refund_type')
                    ->label('رد القيمة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseReturn::REFUND_CASH => 'استرداد نقدي',
                        PurchaseReturn::REFUND_SUPPLIER_BALANCE => 'رصيد المورد',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PurchaseReturn::STATUS_DRAFT => 'مسودة',
                        PurchaseReturn::STATUS_POSTED => 'مرحلة',
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

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        PurchaseReturn::STATUS_DRAFT => 'مسودة',
                        PurchaseReturn::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}