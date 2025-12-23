<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreContactFunctionRequest;
use App\Http\Requests\UpdateContactFunctionRequest;
use App\Models\ContactFunction;
use Inertia\Inertia;

class ContactFunctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $contactFunctions = ContactFunction::orderBy('name')->paginate(15);
        } catch (\Exception $e) {
            Log::error('Error loading contact functions: ' . $e->getMessage());
            $contactFunctions = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                15,
                1
            );
        }

        return Inertia::render('Configurations/ContactFunctions/Index', [
            'contactFunctions' => $contactFunctions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Configurations/ContactFunctions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactFunctionRequest $request)
    {
        ContactFunction::create($request->validated());

        return redirect()->route('contact-functions.index')
            ->with('success', 'Função criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactFunction $contactFunction)
    {
        return Inertia::render('Configurations/ContactFunctions/Edit', [
            'contactFunction' => $contactFunction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactFunctionRequest $request, ContactFunction $contactFunction)
    {
        $contactFunction->update($request->validated());

        return redirect()->route('contact-functions.index')
            ->with('success', 'Função atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactFunction $contactFunction)
    {
        $contactFunction->delete();

        return redirect()->route('contact-functions.index')
            ->with('success', 'Função eliminada com sucesso');
    }
}
