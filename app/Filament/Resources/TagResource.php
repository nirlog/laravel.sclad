<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;
    protected static ?string $navigationLabel = 'Теги';
    protected static ?string $modelLabel = 'Теги';
    protected static ?string $pluralModelLabel = 'Теги';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Проект')->relationship('project', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->label('Название')->maxLength(255)->required(),
            TextInput::make('slug')->label('Slug')->maxLength(255),
            TextInput::make('color')->label('Цвет')->maxLength(255)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Проект')->searchable()->sortable(),
            TextColumn::make('name')->label('Название')->searchable()->sortable(),
            TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
            TextColumn::make('color')->label('Цвет')->searchable()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
