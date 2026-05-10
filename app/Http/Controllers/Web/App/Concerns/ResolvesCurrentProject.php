<?php

namespace App\Http\Controllers\Web\App\Concerns;

use App\Models\Project;
use Illuminate\Http\Request;

trait ResolvesCurrentProject
{
    protected function currentProject(Request $request): Project
    {
        $projects = $request->user()->projects()->where('status', 'active');

        if ($request->filled('project_id')) {
            $project = (clone $projects)->whereKey($request->integer('project_id'))->first();

            if ($project) {
                $request->session()->put('current_project_id', $project->id);

                return $project;
            }

            $request->session()->forget('current_project_id');
        }

        $sessionProjectId = $request->session()->get('current_project_id');

        if ($sessionProjectId) {
            $project = (clone $projects)->whereKey($sessionProjectId)->first();

            if ($project) {
                return $project;
            }

            $request->session()->forget('current_project_id');
        }

        $project = $projects->firstOrFail();
        $request->session()->put('current_project_id', $project->id);

        return $project;
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
