<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialWriteOffResource\Pages;
use App\Models\MaterialWriteOff;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialWriteOffResource extends Resource
{
    protected static ?string $model = MaterialWriteOff::class;
    protected static ?string $navigationLabel = 'Списания материалов';
    protected static ?string $modelLabel = 'Списания материалов';
    protected static ?string $pluralModelLabel = 'Списания материалов';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            Select::make('material_id')->label('Материал')->relationship('material', 'name')->searchable()->preload()->required(),
            DatePicker::make('date')->label('Дата')->required(),
            TextInput::make('quantity')->label('Количество')->numeric(),
            TextInput::make('total_amount')->label('Сумма')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('material.name')->label('Материал')->searchable()->sortable(),
            TextColumn::make('date')->label('Дата')->date('d.m.Y')->sortable(),
            TextColumn::make('quantity')->label('Количество')->numeric()->sortable(),
            TextColumn::make('total_amount')->label('Сумма')->numeric()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialWriteOffs::route('/'),
            'create' => Pages\CreateMaterialWriteOff::route('/create'),
            'edit' => Pages\EditMaterialWriteOff::route('/{record}/edit'),
        ];
    }
}
