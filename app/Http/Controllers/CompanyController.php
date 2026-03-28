<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class CompanyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Companies/Index', [
            'companies' => Company::all(),
        ]);
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->back();
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        $company->update($request->only('name'));

        return redirect()->back();
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        Company::create($request->only('name'));

        return redirect()->back();
    }
}
