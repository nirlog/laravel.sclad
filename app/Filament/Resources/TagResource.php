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
    protected static ?string $navigationLabel = 'Tag';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                Select::make('project_id')->numeric()->required(),
                TextInput::make('name')->maxLength(255),
                TextInput::make('slug')->maxLength(255),
                TextInput::make('color')->maxLength(255)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                TextColumn::make('project_id')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->sortable(),
                TextColumn::make('color')->searchable()->sortable()
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
