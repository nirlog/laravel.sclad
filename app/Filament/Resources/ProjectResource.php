<?php
namespace App\Filament\Resources;
use App\Models\Project;use Filament\Resources\Resource;use Filament\Schemas\Schema;use Filament\Tables\Table;
class ProjectResource extends Resource
{
    protected static ?string $model=Project::class;
    protected static ?string $navigationLabel='Project';
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table { return $table; }
    public static function getPages(): array { return []; }
}
