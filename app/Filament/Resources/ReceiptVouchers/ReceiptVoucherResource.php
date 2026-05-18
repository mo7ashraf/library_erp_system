<?php

namespace App\Filament\Resources\ReceiptVouchers;

use App\Filament\Resources\ReceiptVouchers\Pages\CreateReceiptVoucher;
use App\Filament\Resources\ReceiptVouchers\Pages\ListReceiptVouchers;
use App\Filament\Resources\ReceiptVouchers\Pages\ViewReceiptVoucher;
use App\Filament\Resources\ReceiptVouchers\Schemas\ReceiptVoucherForm;
use App\Filament\Resources\ReceiptVouchers\Schemas\ReceiptVoucherInfolist;
use App\Filament\Resources\ReceiptVouchers\Tables\ReceiptVouchersTable;
use App\Models\ReceiptVoucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReceiptVoucherResource extends Resource
{
    protected static ?string $model = ReceiptVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $recordTitleAttribute = 'voucher_number';

    public static function getModelLabel(): string
    {
        return 'سند قبض';
    }

    public static function getPluralModelLabel(): string
    {
        return 'سندات القبض';
    }

    public static function getNavigationLabel(): string
    {
        return 'سند قبض';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحسابات والمالية';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return ReceiptVoucherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReceiptVoucherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceiptVouchersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceiptVouchers::route('/'),
            'create' => CreateReceiptVoucher::route('/create'),
            'view' => ViewReceiptVoucher::route('/{record}'),
        ];
    }
}