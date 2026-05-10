<?php
namespace App\Filament\Resources;
use App\Models\Tag;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class TagResource extends Resource
{
    protected static ?string $model=Tag::class;
    protected static ?string $navigationLabel='Tag';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
