<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialPurchaseResource\Pages;
use App\Models\MaterialPurchase;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialPurchaseResource extends Resource
{
    protected static ?string $model = MaterialPurchase::class;
    protected static ?string $navigationLabel = 'Покупки материалов';
    protected static ?string $modelLabel = 'Покупки материалов';
    protected static ?string $pluralModelLabel = 'Покупки материалов';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            DatePicker::make('date')->label('Дата')->required(),
            TextInput::make('supplier_name')->label('Поставщик')->maxLength(255),
            TextInput::make('payment_status')->label('Оплата')->maxLength(255),
            TextInput::make('total_amount')->label('Сумма')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('date')->label('Дата')->date('d.m.Y')->sortable(),
            TextColumn::make('supplier_name')->label('Поставщик')->searchable()->sortable(),
            TextColumn::make('payment_status')->label('Оплата')->searchable()->sortable(),
            TextColumn::make('total_amount')->label('Сумма')->numeric()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialPurchases::route('/'),
            'create' => Pages\CreateMaterialPurchase::route('/create'),
            'edit' => Pages\EditMaterialPurchase::route('/{record}/edit'),
        ];
    }
}
