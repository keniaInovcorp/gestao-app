<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->checkPermission('supplier-orders.read');

        $supplierOrders = SupplierOrder::with(['supplier', 'order.client', 'lines.product'])
            ->orderBy('order_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(15);

        return Inertia::render('SupplierOrders/Index', [
            'supplierOrders' => $supplierOrders,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierOrder $supplierOrder): Response
    {
        $this->checkPermission('supplier-orders.read');

        $supplierOrder->load(['supplier', 'order.client', 'lines.product']);
        $this->logActivity($supplierOrder, 'viewed', 'supplier-order', request());

        return Inertia::render('SupplierOrders/Show', [
            'supplierOrder' => $supplierOrder,
        ]);
    }
}
