<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\Entity;
use App\Models\Country;
use App\Services\ViesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EntityController extends Controller
{
    /**
     * Display a listing of entities filtered by type.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'client');

        $entities = Entity::where('type', $type)
            ->with('country')
            ->orderBy('number', 'desc')
            ->paginate(15);

        return Inertia::render('Entities/Index', [
            'entities' => $entities,
            'type' => $type,
        ]);
    }

    /**
     * Show the form for creating a new entity.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'client');

        return Inertia::render('Entities/Create', [
            'type' => $type,
            'countries' => Country::where('status', 'active')->get(),
        ]);
    }

    /**
     * Store a newly created entity in storage.
     *
     * @param StoreEntityRequest $request
     * @return RedirectResponse
     */
    public function store(StoreEntityRequest $request)
    {
        $lastEntity = Entity::where('type', $request->type)
            ->orderBy('number', 'desc')
            ->first();

        $nextNumber = $lastEntity ? (int)$lastEntity->number + 1 : 1;

        Entity::create([
            'type' => $request->type,
            'number' => str_pad($nextNumber, 6, '0', STR_PAD_LEFT),
            'tax_number' => $request->tax_number,
            'name' => $request->name,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'website' => $request->website,
            'email' => $request->email,
            'gdpr_consent' => $request->gdpr_consent ?? false,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        $routeName = $request->type === 'client' ? 'clients.index' : 'suppliers.index';
        return redirect()->route($routeName)
            ->with('success', 'Entidade criada com sucesso');
    }

    /**
     * Display the specified entity.
     *
     * @param Entity $entity
     * @return Response
     */
    public function show(Entity $entity)
    {
        $entity->load('country');

        return Inertia::render('Entities/Show', [
            'entity' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified entity.
     *
     * @param Entity $entity
     * @return Response
     */
    public function edit(Entity $entity)
    {
        return Inertia::render('Entities/Edit', [
            'entity' => $entity->load('country'),
            'countries' => Country::where('status', 'active')->get(),
        ]);
    }

    /**
     * Update the specified entity in storage.
     *
     * @param UpdateEntityRequest $request
     * @param Entity $entity
     * @return RedirectResponse
     */
    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        $data = $request->validated();
        unset($data['gdpr_consent']);
        
        $entity->update($data);

        $routeName = $entity->type === 'client' ? 'clients.index' : 'suppliers.index';
        return redirect()->route($routeName)
            ->with('success', 'Entidade atualizada com sucesso');
    }

    /**
     * Remove the specified entity from storage.
     *
     * @param Entity $entity
     * @return RedirectResponse
     */
    public function destroy(Entity $entity)
    {
        $type = $entity->type;
        $entity->delete();

        $routeName = $type === 'client' ? 'clients.index' : 'suppliers.index';
        return redirect()->route($routeName)
            ->with('success', 'Entidade eliminada com sucesso');
    }

    /**
     * Validate NIF using VIES service.
     *
     * @param Request $request
     * @param ViesService $viesService
     * @return JsonResponse
     */
    public function validateVies(Request $request, ViesService $viesService)
    {
        $request->validate([
            'nif' => ['required', 'string'],
            'country_code' => ['nullable', 'string', 'size:2'],
        ]);

        $result = $viesService->validateNif(
            $request->nif,
            $request->country_code ?? 'PT'
        );

        if ($result === null) {
            return response()->json([
                'valid' => false,
                'error' => 'Serviço VIES indisponível'
            ]);
        }

        return response()->json($result);
    }
}
