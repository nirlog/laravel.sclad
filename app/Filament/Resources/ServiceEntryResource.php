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
    protected static ?string $navigationLabel = 'ServiceEntry';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                Select::make('project_id')->numeric()->required(),
                Select::make('contractor_id')->numeric()->required(),
                DatePicker::make('date')->required(),
                TextInput::make('name')->maxLength(255),
                TextInput::make('pricing_type')->maxLength(255),
                TextInput::make('total_amount')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                TextColumn::make('project_id')->searchable()->sortable(),
                TextColumn::make('contractor_id')->searchable()->sortable(),
                TextColumn::make('date')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('pricing_type')->searchable()->sortable(),
                TextColumn::make('total_amount')->searchable()->sortable()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceEntrys::route('/'),
            'create' => Pages\CreateServiceEntry::route('/create'),
            'edit' => Pages\EditServiceEntry::route('/{record}/edit'),
        ];
    }
}
