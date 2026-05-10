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
    protected static ?string $navigationLabel = 'MaterialPurchase';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                Select::make('project_id')->numeric()->required(),
                DatePicker::make('date')->required(),
                TextInput::make('supplier_name')->maxLength(255),
                TextInput::make('payment_status')->maxLength(255),
                TextInput::make('total_amount')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                TextColumn::make('project_id')->searchable()->sortable(),
                TextColumn::make('date')->searchable()->sortable(),
                TextColumn::make('supplier_name')->searchable()->sortable(),
                TextColumn::make('payment_status')->searchable()->sortable(),
                TextColumn::make('total_amount')->searchable()->sortable()
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
