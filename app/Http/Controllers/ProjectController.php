<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Projects/Index', [
            'projects' => Project::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        Project::create($request->only('name', 'company_id'));

        return redirect()->back();
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        $project->update($request->only('name', 'company_id'));

        return redirect()->back();
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->back();
    }
}
