<?php

namespace App\Filament\Resources\PaymentVouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\PaymentVoucher;
use App\Models\TreasuryTransaction;
use Filament\Actions\Action;

class PaymentVouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('voucher_date', 'desc')
            ->columns([
                TextColumn::make('voucher_number')
                    ->label('رقم السند')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('voucher_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('party_name')
                    ->label('الطرف')
                    ->searchable(),

                TextColumn::make('payment_channel')
                    ->label('طريقة الصرف')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        TreasuryTransaction::CHANNEL_CASH => 'خزينة',
                        TreasuryTransaction::CHANNEL_BANK => 'بنك',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2) . ' ج.م')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PaymentVoucher::STATUS_DRAFT => 'مسودة',
                        PaymentVoucher::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    })
                    ->badge()
                    ->sortable(),

                TextColumn::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('payment_channel')
                    ->label('طريقة الصرف')
                    ->options([
                        TreasuryTransaction::CHANNEL_CASH => 'خزينة',
                        TreasuryTransaction::CHANNEL_BANK => 'بنك',
                    ]),

                SelectFilter::make('party_type')
                    ->label('نوع الطرف')
                    ->options([
                        PaymentVoucher::PARTY_CUSTOMER => 'عميل',
                        PaymentVoucher::PARTY_SUPPLIER => 'مورد',
                        PaymentVoucher::PARTY_OTHER => 'أخرى',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),

                Action::make('print_receipt')
                ->label('طباعة')
                ->url(fn (PaymentVoucher $record): string => route('admin.prints.payment-vouchers.receipt', $record))
                ->openUrlInNewTab(),
            ]);
    }
}