<?php
namespace App\Filament\Resources;
use App\Models\Contractor;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class ContractorResource extends Resource
{
    protected static ?string $model=Contractor::class;
    protected static ?string $navigationLabel='Contractor';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
