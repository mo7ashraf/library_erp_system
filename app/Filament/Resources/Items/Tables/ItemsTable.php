<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->circular(),

                TextColumn::make('code')
                    ->label('كود الصنف')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barcode')
                    ->label('الباركود')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('اسم الصنف')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('group.name')
                    ->label('المجموعة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('subgroup.name')
                    ->label('المجموعة الفرعية')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('baseUnit.name')
                    ->label('الوحدة')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('purchase_price')
                    ->label('سعر الشراء')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('student_price')
                    ->label('سعر الطالب')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('teacher_price')
                    ->label('سعر المدرس')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('representative_price')
                    ->label('سعر المندوب')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('retail_price')
                    ->label('سعر القطاعي')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('wholesale_price')
                    ->label('سعر الجملة')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('reorder_level')
                    ->label('حد الطلب')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('continue_balance')
                    ->label('استمرار الرصيد')
                    ->boolean()
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
                SelectFilter::make('item_group_id')
                    ->label('مجموعة الصنف')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('item_subgroup_id')
                    ->label('المجموعة الفرعية')
                    ->relationship('subgroup', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('نشط فقط')
                    ->falseLabel('غير نشط فقط'),

                TernaryFilter::make('continue_balance')
                    ->label('استمرار الرصيد')
                    ->placeholder('الكل')
                    ->trueLabel('أصناف مستمرة الرصيد')
                    ->falseLabel('أصناف غير مستمرة الرصيد'),
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