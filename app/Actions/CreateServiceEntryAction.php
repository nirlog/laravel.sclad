<?php

namespace App\Actions;

use App\Actions\Concerns\ValidatesProjectScope;
use App\Models\Project;
use App\Models\ServiceEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateServiceEntryAction
{
    use ValidatesProjectScope;

    public function execute(array $data): ServiceEntry
    {
        return DB::transaction(function () use ($data): ServiceEntry {
            $project = Project::findOrFail($data['project_id']);
            $tagIds = $data['tag_ids'] ?? [];
            $this->assertTagsBelongToProject($project, $tagIds);

            if (! empty($data['contractor_id']) && ! $project->contractors()->whereKey($data['contractor_id'])->exists()) {
                throw ValidationException::withMessages([
                    'contractor_id' => 'Исполнитель не принадлежит выбранному проекту.',
                ]);
            }

            unset($data['tag_ids']);
            $data['project_id'] = $project->id;
            $data['total_amount'] = $this->total($data);
            $data['paid_amount'] = $data['paid_amount'] ?? ((($data['payment_status'] ?? 'paid') === 'paid') ? $data['total_amount'] : 0);

            $entry = ServiceEntry::create($data);
            $entry->tags()->sync($tagIds);

            return $entry->load('tags', 'contractor');
        });
    }

    private function total(array $data): float
    {
        return match ($data['pricing_type']) {
            'hourly' => round((float) $data['hours'] * (float) $data['hourly_rate'], 2),
            'unit' => round((float) $data['quantity'] * (float) $data['unit_price'], 2),
            default => round((float) $data['total_amount'], 2),
        };
    }
}
