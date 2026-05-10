<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App\Concerns\ResolvesCurrentProject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    use ResolvesCurrentProject;

    public function __invoke(Request $request): Response
    {
        $project = $this->currentProject($request);

        return Inertia::render('App/Settings/Index', $this->sharedProjects($request, $project) + [
            'tags' => $project->tags()->orderBy('name')->get(),
            'units' => \App\Models\Unit::orderBy('short_name')->get(),
        ]);
    }
}
