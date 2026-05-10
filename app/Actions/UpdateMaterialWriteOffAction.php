<?php
namespace App\Actions;
use App\Models\MaterialWriteOff;use Illuminate\Support\Facades\DB;
class UpdateMaterialWriteOffAction{public function execute(MaterialWriteOff $writeOff,array $data): MaterialWriteOff{return DB::transaction(function() use($writeOff,$data){$writeOff->movement()->delete();$writeOff->tags()->detach();$writeOff->delete();return app(CreateMaterialWriteOffAction::class)->execute($data);});}}
