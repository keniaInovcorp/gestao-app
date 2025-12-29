<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Models\Country;
use Inertia\Inertia;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $countries = Country::orderBy('name')->paginate(15);
        } catch (\Exception $e) {
            Log::error('Error loading countries: ' . $e->getMessage());
            $countries = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                15,
                1
            );
        }

        return Inertia::render('Configurations/Countries/Index', [
            'countries' => $countries,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('countries.create');

        return Inertia::render('Configurations/Countries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        $this->checkPermission('countries.create');

        $country = Country::create($request->validated());
        $this->logActivity($country, 'created', 'country', $request);

        return redirect()->route('countries.index')
            ->with('success', 'País criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        $this->checkPermission('countries.read');

        $this->logActivity($country, 'viewed', 'country', request());

        return Inertia::render('Configurations/Countries/Show', [
            'country' => $country,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit(Country $country)
    {
        $this->checkPermission('countries.update');

        return Inertia::render('Configurations/Countries/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        $this->checkPermission('countries.update');

        $country->update($request->validated());
        $this->logActivity($country, 'updated', 'country', $request);

        return redirect()->route('countries.index')
            ->with('success', 'País atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Country $country)
    {
        $this->checkPermission('countries.delete');

        $countryName = $country->name;
        $country->delete();
        $this->logActivity(null, 'deleted', 'country', request(), "Deleted country {$countryName}");

        return redirect()->route('countries.index')
            ->with('success', 'País eliminado com sucesso');
    }
}
