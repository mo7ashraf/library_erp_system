<?php

namespace App\Filament\Resources\PaymentVouchers\Schemas;

use App\Models\PaymentVoucher;
use App\Models\TreasuryTransaction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentVoucherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('voucher_number')
                    ->label('رقم السند'),

                TextEntry::make('voucher_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('party_name')
                    ->label('الطرف')
                    ->placeholder('-'),

                TextEntry::make('payment_channel')
                    ->label('طريقة الصرف')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        TreasuryTransaction::CHANNEL_CASH => 'خزينة',
                        TreasuryTransaction::CHANNEL_BANK => 'بنك',
                        default => '-',
                    }),

                TextEntry::make('cashbox.name')
                    ->label('الخزينة')
                    ->placeholder('-'),

                TextEntry::make('bankAccount.account_name')
                    ->label('الحساب البنكي')
                    ->placeholder('-'),

                TextEntry::make('amount')
                    ->label('المبلغ')
                    ->money('EGP'),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        PaymentVoucher::STATUS_DRAFT => 'مسودة',
                        PaymentVoucher::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    }),

                TextEntry::make('treasuryTransaction.transaction_number')
                    ->label('رقم الحركة المالية')
                    ->placeholder('-'),

                TextEntry::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('-'),

                TextEntry::make('description')
                    ->label('البيان')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('notes')
                    ->label('ملاحظات')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}