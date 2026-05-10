<?php
namespace App\Filament\Resources;
use App\Models\Unit;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class UnitResource extends Resource
{
    protected static ?string $model=Unit::class;
    protected static ?string $navigationLabel='Unit';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
