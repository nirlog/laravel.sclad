<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryMovementResource\Pages;
use App\Models\InventoryMovement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryMovementResource extends Resource
{
    protected static ?string $model = InventoryMovement::class;
    protected static ?string $navigationLabel = 'Движения склада';
    protected static ?string $modelLabel = 'Движения склада';
    protected static ?string $pluralModelLabel = 'Движения склада';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            Select::make('material_id')->label('Материал')->relationship('material', 'name')->searchable()->preload()->required(),
            DatePicker::make('date')->label('Дата')->required(),
            TextInput::make('type')->label('Тип')->maxLength(255),
            TextInput::make('quantity')->label('Количество')->numeric(),
            TextInput::make('amount')->label('Сумма')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('material.name')->label('Материал')->searchable()->sortable(),
            TextColumn::make('date')->label('Дата')->date('d.m.Y')->sortable(),
            TextColumn::make('type')->label('Тип')->searchable()->sortable(),
            TextColumn::make('quantity')->label('Количество')->numeric()->sortable(),
            TextColumn::make('amount')->label('Сумма')->numeric()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryMovements::route('/'),
            'create' => Pages\CreateInventoryMovement::route('/create'),
            'edit' => Pages\EditInventoryMovement::route('/{record}/edit'),
        ];
    }
}
