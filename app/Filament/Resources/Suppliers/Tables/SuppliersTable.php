<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('code')
                    ->label('كود المورد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('اسم المورد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mobile')
                    ->label('الموبايل')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('governorate')
                    ->label('المحافظة')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('opening_balance')
                    ->label('الرصيد الافتتاحي')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('balance_type')
                    ->label('نوع الرصيد')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'debit' => 'مدين',
                        'credit' => 'دائن',
                        default => '-',
                    })
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('balance_type')
                    ->label('نوع الرصيد')
                    ->options([
                        'debit' => 'مدين',
                        'credit' => 'دائن',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('نشط فقط')
                    ->falseLabel('غير نشط فقط'),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}