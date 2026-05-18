<?php

namespace App\Filament\Resources\PaymentVouchers;

use App\Filament\Resources\PaymentVouchers\Pages\CreatePaymentVoucher;
use App\Filament\Resources\PaymentVouchers\Pages\ListPaymentVouchers;
use App\Filament\Resources\PaymentVouchers\Pages\ViewPaymentVoucher;
use App\Filament\Resources\PaymentVouchers\Schemas\PaymentVoucherForm;
use App\Filament\Resources\PaymentVouchers\Schemas\PaymentVoucherInfolist;
use App\Filament\Resources\PaymentVouchers\Tables\PaymentVouchersTable;
use App\Models\PaymentVoucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentVoucherResource extends Resource
{
    protected static ?string $model = PaymentVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $recordTitleAttribute = 'voucher_number';

    public static function getModelLabel(): string
    {
        return 'سند صرف';
    }

    public static function getPluralModelLabel(): string
    {
        return 'سندات الصرف';
    }

    public static function getNavigationLabel(): string
    {
        return 'سند صرف';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحسابات والمالية';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentVoucherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentVoucherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentVouchersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentVouchers::route('/'),
            'create' => CreatePaymentVoucher::route('/create'),
            'view' => ViewPaymentVoucher::route('/{record}'),
        ];
    }
}