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
use Illuminate\Support\Facades\DB;
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
        $resourceName = $type === 'client' ? 'clients' : 'suppliers';
        
        $this->checkPermission("{$resourceName}.read");

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
        $resourceName = $type === 'client' ? 'clients' : 'suppliers';
        
        $this->checkPermission("{$resourceName}.create");

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
        $resourceName = $request->type === 'client' ? 'clients' : 'suppliers';
        $this->checkPermission("{$resourceName}.create");

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
        $resourceName = $entity->type === 'client' ? 'clients' : 'suppliers';
        $this->checkPermission("{$resourceName}.read");

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
        $resourceName = $entity->type === 'client' ? 'clients' : 'suppliers';
        $this->checkPermission("{$resourceName}.update");

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
        $resourceName = $entity->type === 'client' ? 'clients' : 'suppliers';
        $this->checkPermission("{$resourceName}.update");

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
        $resourceName = $entity->type === 'client' ? 'clients' : 'suppliers';
        $this->checkPermission("{$resourceName}.delete");

        $errors = [];

        $contactsCount = DB::table('contacts')->where('entity_id', $entity->id)->count();
        if ($contactsCount > 0) {
            $errors[] = "{$contactsCount} contacto(s)";
        }

        $ordersAsClientCount = DB::table('orders')->where('client_id', $entity->id)->count();
        if ($ordersAsClientCount > 0) {
            $errors[] = "{$ordersAsClientCount} encomenda(s) como cliente";
        }

        $quotationsAsClientCount = DB::table('quotations')->where('client_id', $entity->id)->count();
        if ($quotationsAsClientCount > 0) {
            $errors[] = "{$quotationsAsClientCount} proposta(s) como cliente";
        }

        $orderLinesAsSupplierCount = DB::table('order_lines')->where('supplier_id', $entity->id)->count();
        if ($orderLinesAsSupplierCount > 0) {
            $errors[] = "{$orderLinesAsSupplierCount} linha(s) de encomenda como fornecedor";
        }

        $quotationLinesAsSupplierCount = DB::table('quotation_lines')->where('supplier_id', $entity->id)->count();
        if ($quotationLinesAsSupplierCount > 0) {
            $errors[] = "{$quotationLinesAsSupplierCount} linha(s) de proposta como fornecedor";
        }

        $supplierOrdersCount = DB::table('supplier_orders')->where('supplier_id', $entity->id)->count();
        if ($supplierOrdersCount > 0) {
            $errors[] = "{$supplierOrdersCount} encomenda(s) fornecedor";
        }

        $supplierInvoicesCount = DB::table('supplier_invoices')->where('supplier_id', $entity->id)->count();
        if ($supplierInvoicesCount > 0) {
            $errors[] = "{$supplierInvoicesCount} fatura(s) fornecedor";
        }

        if (!empty($errors)) {
            $errorMessage = "Não pode eliminar esta entidade porque está associada a: " . implode(', ', $errors) . ".";
            $type = $entity->type;
            $routeName = $type === 'client' ? 'clients.index' : 'suppliers.index';
            return redirect()->route($routeName)
                ->with('error', $errorMessage);
        }

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
