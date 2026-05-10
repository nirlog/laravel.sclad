<?php
namespace App\Actions;
use App\Models\ServiceEntry;use Illuminate\Support\Facades\DB;
class UpdateServiceEntryAction extends CreateServiceEntryAction{public function executeUpdate(ServiceEntry $entry,array $data): ServiceEntry{return DB::transaction(function() use($entry,$data){$entry->tags()->detach();$entry->delete();return $this->execute($data);});}}
