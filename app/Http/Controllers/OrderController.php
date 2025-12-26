<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\SupplierOrder;
use App\Models\Entity;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $orders = Order::with(['client', 'lines.product.vatRate'])
            ->orderBy('order_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(15);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $clients = Entity::where('type', 'client')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 'active')
            ->with('vatRate')
            ->orderBy('reference')
            ->get();

        $suppliers = Entity::where('type', 'supplier')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return Inertia::render('Orders/Create', [
            'clients' => $clients,
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $lastOrder = Order::orderBy('number', 'desc')->first();
        $year = Carbon::now()->format('Y');
        
        if ($lastOrder && str_starts_with($lastOrder->number, $year)) {
            $lastNumber = (int) substr($lastOrder->number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        
        $number = $year . '/' . $nextNumber;

        $order = Order::create([
            'number' => $number,
            'order_date' => $request->order_date,
            'client_id' => $request->client_id,
            'validity' => $request->validity,
            'status' => $request->status,
        ]);

        foreach ($request->lines as $line) {
            $order->lines()->create([
                'product_id' => $line['product_id'],
                'supplier_id' => $line['supplier_id'] ?? null,
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
            ]);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Encomenda criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): Response
    {
        $order->load(['client', 'lines.product.vatRate', 'lines.supplier']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): Response
    {
        $order->load(['client', 'lines.product.vatRate', 'lines.supplier']);

        $clients = Entity::where('type', 'client')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 'active')
            ->with('vatRate')
            ->orderBy('reference')
            ->get();

        $suppliers = Entity::where('type', 'supplier')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $orderData = $order->toArray();
        if ($order->order_date) {
            $orderData['order_date'] = $order->order_date->format('Y-m-d');
        }
        if ($order->validity) {
            $orderData['validity'] = $order->validity->format('Y-m-d');
        }

        return Inertia::render('Orders/Edit', [
            'order' => $orderData,
            'clients' => $clients,
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $order->update([
            'order_date' => $request->order_date,
            'client_id' => $request->client_id,
            'status' => $request->status,
        ]);

        $order->lines()->delete();

        foreach ($request->lines as $line) {
            $order->lines()->create([
                'product_id' => $line['product_id'],
                'supplier_id' => $line['supplier_id'] ?? null,
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
            ]);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Encomenda atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Encomenda eliminada com sucesso');
    }

    /**
     * Generate and download PDF for the specified order.
     */
    public function pdf(Order $order)
    {
        $order->load(['client.country', 'lines.product.vatRate', 'lines.supplier']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('orders.pdf', [
            'order' => $order,
        ]);

        $filename = 'encomenda-' . str_replace(['/', '\\'], '-', $order->number) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Convert a closed order to supplier orders.
     */
    public function convertToSupplierOrder(Order $order): RedirectResponse
    {
        if ($order->status !== 'closed') {
            return redirect()->route('orders.index')
                ->with('error', 'Apenas encomendas fechadas podem ser convertidas em encomendas fornecedor');
        }

        $order->load(['lines.product', 'lines.supplier']);

        $linesBySupplier = [];
        foreach ($order->lines as $line) {
            if (!$line->supplier_id) {
                continue;
            }
            
            if (!isset($linesBySupplier[$line->supplier_id])) {
                $linesBySupplier[$line->supplier_id] = [];
            }
            
            $linesBySupplier[$line->supplier_id][] = $line;
        }

        if (empty($linesBySupplier)) {
            return redirect()->route('orders.index')
                ->with('error', 'A encomenda não tem linhas com fornecedor associado');
        }

        $year = Carbon::now()->format('Y');
        $createdCount = 0;

        foreach ($linesBySupplier as $supplierId => $lines) {
            $lastSupplierOrder = SupplierOrder::orderBy('number', 'desc')->first();
            
            if ($lastSupplierOrder && str_starts_with($lastSupplierOrder->number, $year)) {
                $lastNumber = (int) substr($lastSupplierOrder->number, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }
            
            $number = $year . '/' . $nextNumber;

            $supplierOrder = SupplierOrder::create([
                'number' => $number,
                'order_date' => Carbon::now(),
                'supplier_id' => $supplierId,
                'order_id' => $order->id,
                'status' => 'closed',
            ]);

            foreach ($lines as $line) {
                $supplierOrder->lines()->create([
                    'product_id' => $line->product_id,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                ]);
            }

            $createdCount++;
        }

        return redirect()->route('supplier-orders.index')
            ->with('success', "Encomenda convertida com sucesso. Foram criadas {$createdCount} encomenda(s) fornecedor.");
    }
}
