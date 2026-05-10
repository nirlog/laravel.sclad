<?php

namespace App\Http\Controllers\Web\App\Concerns;

use App\Models\Project;
use Illuminate\Http\Request;

trait ResolvesCurrentProject
{
    protected function currentProject(Request $request): Project
    {
        $query = $request->user()->projects()->where('status', 'active');

        if ($request->filled('project_id')) {
            $query->whereKey($request->integer('project_id'));
        }

        return $query->firstOrFail();
    }

    protected function sharedProjects(Request $request, Project $project): array
    {
        return [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(['id', 'name', 'status']),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
