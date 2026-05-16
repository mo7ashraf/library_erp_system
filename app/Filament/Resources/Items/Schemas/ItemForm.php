<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('البيانات الأساسية')
                    ->columns(3)
                    ->schema([
                        TextInput::make('code')
                            ->label('كود الصنف')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('origin_code')
                            ->label('كود المنشأ')
                            ->maxLength(100),

                        TextInput::make('barcode')
                            ->label('الباركود الدولي')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('اسم الصنف')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->columnSpanFull(),

                        Select::make('item_group_id')
                            ->label('مجموعة الصنف')
                            ->relationship('group', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('item_subgroup_id')
                            ->label('المجموعة الفرعية')
                            ->relationship('subgroup', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('source')
                            ->label('مصدر الصنف')
                            ->maxLength(255),

                        TextInput::make('publisher')
                            ->label('الناشر / المورد العلمي')
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),

                        Toggle::make('continue_balance')
                            ->label('استمرار أرصدة الصنف')
                            ->default(true),
                    ]),

                Section::make('الوحدات')
                    ->columns(3)
                    ->schema([
                        Select::make('base_unit_id')
                            ->label('الوحدة الصغرى / الأساسية')
                            ->relationship('baseUnit', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('middle_unit_id')
                            ->label('الوحدة الوسطى')
                            ->relationship('middleUnit', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('large_unit_id')
                            ->label('الوحدة الكبرى')
                            ->relationship('largeUnit', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('units_per_middle')
                            ->label('عدد الوحدات داخل الوحدة الوسطى')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('units_per_large')
                            ->label('عدد الوحدات داخل الوحدة الكبرى')
                            ->numeric()
                            ->minValue(0),
                    ]),

                Section::make('الشراء والتكلفة')
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchase_price')
                            ->label('سعر الشراء')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('first_discount_percent')
                            ->label('خصم أول %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),

                        TextInput::make('second_discount_percent')
                            ->label('خصم ثاني %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),

                        TextInput::make('net_purchase_price')
                            ->label('صافي الشراء')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('total_cost')
                            ->label('إجمالي التكلفة')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('profit_margin_percent')
                            ->label('هامش الربح %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                    ]),

                Section::make('أسعار البيع')
                    ->columns(3)
                    ->schema([
                        TextInput::make('student_price')
                            ->label('سعر البيع للطالب')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('teacher_price')
                            ->label('سعر البيع للمدرس')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('representative_price')
                            ->label('سعر البيع للمندوب')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('retail_price')
                            ->label('سعر القطاعي')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('wholesale_price')
                            ->label('سعر الجملة')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('teacher_discount_percent')
                            ->label('خصم المدرس %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),

                        TextInput::make('representative_discount_percent')
                            ->label('خصم المندوب %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),

                        TextInput::make('return_percent')
                            ->label('نسبة المردودات %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                    ]),

                Section::make('حدود المخزون')
                    ->columns(3)
                    ->schema([
                        TextInput::make('max_stock')
                            ->label('الحد الأقصى')
                            ->numeric()
                            ->default(0),

                        TextInput::make('min_stock')
                            ->label('الحد الأدنى')
                            ->numeric()
                            ->default(0),

                        TextInput::make('reorder_level')
                            ->label('حد الطلب')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('الصورة والملاحظات')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('صورة الصنف')
                            ->image()
                            ->directory('items')
                            ->imageEditor(),

                        Textarea::make('details')
                            ->label('مفردات / تفاصيل الصنف')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}