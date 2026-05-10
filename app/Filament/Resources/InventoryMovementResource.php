<?php
namespace App\Filament\Resources;
use App\Models\InventoryMovement;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class InventoryMovementResource extends Resource
{
    protected static ?string $model=InventoryMovement::class;
    protected static ?string $navigationLabel='InventoryMovement';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
