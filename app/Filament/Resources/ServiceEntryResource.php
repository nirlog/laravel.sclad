<?php
namespace App\Filament\Resources;
use App\Models\ServiceEntry;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class ServiceEntryResource extends Resource
{
    protected static ?string $model=ServiceEntry::class;
    protected static ?string $navigationLabel='ServiceEntry';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
