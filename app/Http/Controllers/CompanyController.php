<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    private function checkAdminAccess(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Acesso negado. Apenas administradores podem gerir a empresa.');
        }
    }

    /**
     * Show the form for editing the company.
     *
     * @return Response
     */
    public function edit(): Response
    {
        $this->checkAdminAccess();

        $company = Company::first();

        return Inertia::render('Configurations/Company/Edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the company in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'O nome é obrigatório.',
        ]);

        $company = Company::firstOrNew([]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('private')->delete($company->logo);
            }

            $logoPath = $request->file('logo')->store('company', 'private');
            $company->logo = $logoPath;
        }

        $company->name = $request->name;
        $company->address = $request->address;
        $company->postal_code = $request->postal_code;
        $company->city = $request->city;
        $company->tax_number = $request->tax_number;
        $company->save();

        return redirect()->route('company.edit')
            ->with('success', 'Dados da empresa atualizados com sucesso');
    }

    /**
     * Get company logo for display.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function logo()
    {
        $company = Company::first();

        if (!$company || !$company->logo) {
            abort(404);
        }

        $path = storage_path('app/private/' . $company->logo);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
