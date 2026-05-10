<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceEntryResource\Pages;
use App\Models\ServiceEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceEntryResource extends Resource
{
    protected static ?string $model = ServiceEntry::class;
    protected static ?string $navigationLabel = 'Услуги и работы';
    protected static ?string $modelLabel = 'Услуги и работы';
    protected static ?string $pluralModelLabel = 'Услуги и работы';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            Select::make('contractor_id')->label('Исполнитель')->relationship('contractor', 'name')->searchable()->preload()->nullable(),
            DatePicker::make('date')->label('Дата')->required(),
            TextInput::make('name')->label('Название')->maxLength(255)->required(),
            TextInput::make('pricing_type')->label('Тип расчёта')->maxLength(255),
            TextInput::make('total_amount')->label('Сумма')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('contractor.name')->label('Исполнитель')->searchable()->sortable(),
            TextColumn::make('date')->label('Дата')->date('d.m.Y')->sortable(),
            TextColumn::make('name')->label('Название')->searchable()->sortable(),
            TextColumn::make('pricing_type')->label('Тип расчёта')->searchable()->sortable(),
            TextColumn::make('total_amount')->label('Сумма')->numeric()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceEntries::route('/'),
            'create' => Pages\CreateServiceEntry::route('/create'),
            'edit' => Pages\EditServiceEntry::route('/{record}/edit'),
        ];
    }
}
