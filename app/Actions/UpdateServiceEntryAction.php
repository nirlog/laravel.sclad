<?php

namespace App\Actions;

use App\Models\ServiceEntry;
use Illuminate\Support\Facades\DB;

class UpdateServiceEntryAction
{
    public function execute(ServiceEntry $entry, array $data): ServiceEntry
    {
        return DB::transaction(function () use ($entry, $data): ServiceEntry {
            $entry->tags()->detach();
            $entry->delete();
            $data['project_id'] = $entry->project_id;

            return app(CreateServiceEntryAction::class)->execute($data);
        });
    }
}
