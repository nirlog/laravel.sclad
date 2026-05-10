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
    protected static ?string $navigationLabel = 'MaterialWriteOff';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                Select::make('project_id')->numeric()->required(),
                Select::make('material_id')->numeric()->required(),
                DatePicker::make('date')->required(),
                TextInput::make('quantity')->numeric(),
                TextInput::make('total_amount')->numeric()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                TextColumn::make('project_id')->searchable()->sortable(),
                TextColumn::make('material_id')->searchable()->sortable(),
                TextColumn::make('date')->searchable()->sortable(),
                TextColumn::make('quantity')->searchable()->sortable(),
                TextColumn::make('total_amount')->searchable()->sortable()
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
