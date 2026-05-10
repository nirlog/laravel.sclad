<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationLabel = 'Material';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                Select::make('project_id')->numeric()->required(),
                Select::make('unit_id')->numeric()->required(),
                TextInput::make('name')->maxLength(255),
                TextInput::make('sku')->maxLength(255)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                TextColumn::make('project_id')->searchable()->sortable(),
                TextColumn::make('unit_id')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')->searchable()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
