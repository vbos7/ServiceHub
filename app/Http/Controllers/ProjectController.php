<?php

namespace App\Http\Controllers;

use App\Models\{Company, Project};
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Projects/Index', [
            'projects'  => Project::with('company')->get(),
            'companies' => Company::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        Project::create($request->only('name', 'company_id'));

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        $project->update($request->only('name', 'company_id'));

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully.');
    }
}
