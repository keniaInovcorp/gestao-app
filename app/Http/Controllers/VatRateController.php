<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreVatRateRequest;
use App\Http\Requests\UpdateVatRateRequest;
use App\Models\VatRate;
use Inertia\Inertia;

class VatRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('vat-rates.read');

        try {
            $vatRates = VatRate::orderBy('percentage')->paginate(15);
        } catch (\Exception $e) {
            Log::error('Error loading VAT rates: ' . $e->getMessage());
            $vatRates = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                15,
                1
            );
        }

        return Inertia::render('Configurations/VatRates/Index', [
            'vatRates' => $vatRates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('vat-rates.create');

        return Inertia::render('Configurations/VatRates/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVatRateRequest $request)
    {
        $this->checkPermission('vat-rates.create');

        VatRate::create($request->validated());

        return redirect()->route('vat-rates.index')
            ->with('success', 'Taxa de IVA criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(VatRate $vatRate)
    {
        $this->checkPermission('vat-rates.read');

        return Inertia::render('Configurations/VatRates/Show', [
            'vatRate' => $vatRate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VatRate $vatRate)
    {
        $this->checkPermission('vat-rates.update');

        return Inertia::render('Configurations/VatRates/Edit', [
            'vatRate' => $vatRate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVatRateRequest $request, VatRate $vatRate)
    {
        $this->checkPermission('vat-rates.update');

        $vatRate->update($request->validated());

        return redirect()->route('vat-rates.index')
            ->with('success', 'Taxa de IVA atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VatRate $vatRate)
    {
        $this->checkPermission('vat-rates.delete');

        $vatRate->delete();

        return redirect()->route('vat-rates.index')
            ->with('success', 'Taxa de IVA eliminada com sucesso');
    }
}
