<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationLabel = 'Проекты';
    protected static ?string $modelLabel = 'Проекты';
    protected static ?string $pluralModelLabel = 'Проекты';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('Владелец')->relationship('user', 'email')->searchable()->preload()->default(fn () => auth()->id())->required(),
            TextInput::make('name')->label('Название')->maxLength(255)->required(),
            TextInput::make('address')->label('Адрес')->maxLength(255),
            TextInput::make('status')->label('Статус')->maxLength(255)->required()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.email')->label('Владелец')->searchable()->sortable(),
            TextColumn::make('name')->label('Название')->searchable()->sortable(),
            TextColumn::make('address')->label('Адрес')->searchable()->sortable(),
            TextColumn::make('status')->label('Статус')->searchable()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
