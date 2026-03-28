<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\{RedirectResponse, Request};

class ProjectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        Project::create($request->only('name', 'company_id'));

        return redirect()->back();
    }
}
