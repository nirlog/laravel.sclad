<?php
namespace App\Actions;
use App\Models\MaterialWriteOff;use Illuminate\Support\Facades\DB;
class DeleteMaterialWriteOffAction{public function execute(MaterialWriteOff $writeOff): void{DB::transaction(function() use($writeOff){$writeOff->movement()->delete();$writeOff->tags()->detach();$writeOff->delete();});}}
