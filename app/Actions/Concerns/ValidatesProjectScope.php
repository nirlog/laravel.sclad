<?php

namespace App\Actions\Concerns;

use App\Models\Project;
use Illuminate\Validation\ValidationException;

trait ValidatesProjectScope
{
    protected function assertTagsBelongToProject(Project $project, array $tagIds): void
    {
        if ($tagIds === []) {
            return;
        }

        $validCount = $project->tags()->whereIn('id', $tagIds)->count();

        if ($validCount !== count(array_unique($tagIds))) {
            throw ValidationException::withMessages([
                'tag_ids' => 'Один или несколько тегов не принадлежат выбранному проекту.',
            ]);
        }
    }
}
