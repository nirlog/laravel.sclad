<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractorResource\Pages;
use App\Models\Contractor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContractorResource extends Resource
{
    protected static ?string $model = Contractor::class;
    protected static ?string $navigationLabel = 'Исполнители';
    protected static ?string $modelLabel = 'Исполнители';
    protected static ?string $pluralModelLabel = 'Исполнители';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->label('Имя')->maxLength(255)->required(),
            TextInput::make('phone')->label('Телефон')->maxLength(255),
            TextInput::make('email')->label('Email')->maxLength(255)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('name')->label('Имя')->searchable()->sortable(),
            TextColumn::make('phone')->label('Телефон')->searchable()->sortable(),
            TextColumn::make('email')->label('Email')->searchable()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractors::route('/'),
            'create' => Pages\CreateContractor::route('/create'),
            'edit' => Pages\EditContractor::route('/{record}/edit'),
        ];
    }
}
