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
    protected static ?string $navigationLabel = 'Материалы';
    protected static ?string $modelLabel = 'Материалы';
    protected static ?string $pluralModelLabel = 'Материалы';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            Select::make('unit_id')->label('Единица')->relationship('unit', 'short_name')->searchable()->preload()->required(),
            TextInput::make('name')->label('Название')->maxLength(255)->required(),
            TextInput::make('sku')->label('Артикул')->maxLength(255)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('unit.short_name')->label('Единица')->searchable()->sortable(),
            TextColumn::make('name')->label('Название')->searchable()->sortable(),
            TextColumn::make('sku')->label('Артикул')->searchable()->sortable()
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
