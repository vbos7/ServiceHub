<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Companies/Index', [
            'companies' => Company::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        Company::create($request->only('name'));

        return redirect()->back();
    }
}
