<?php
namespace App\Filament\Resources;
use App\Models\MaterialWriteOff;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class MaterialWriteOffResource extends Resource
{
    protected static ?string $model=MaterialWriteOff::class;
    protected static ?string $navigationLabel='MaterialWriteOff';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
