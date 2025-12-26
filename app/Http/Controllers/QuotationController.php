<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Quotation;
use App\Models\Entity;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $quotations = Quotation::with(['client', 'lines.product.vatRate'])
            ->orderBy('quotation_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(15);

        return Inertia::render('Quotations/Index', [
            'quotations' => $quotations,
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

        return Inertia::render('Quotations/Create', [
            'clients' => $clients,
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuotationRequest $request): RedirectResponse
    {
        $lastQuotation = Quotation::orderBy('number', 'desc')->first();
        $year = Carbon::now()->format('Y');
        
        if ($lastQuotation && str_starts_with($lastQuotation->number, $year)) {
            $lastNumber = (int) substr($lastQuotation->number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        
        $number = $year . '/' . $nextNumber;

        $quotation = Quotation::create([
            'number' => $number,
            'quotation_date' => $request->quotation_date,
            'client_id' => $request->client_id,
            'validity' => $request->validity,
            'status' => $request->status,
        ]);

        foreach ($request->lines as $line) {
            $quotation->lines()->create([
                'product_id' => $line['product_id'],
                'supplier_id' => $line['supplier_id'] ?? null,
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'cost_price' => $line['cost_price'] ?? null,
            ]);
        }

        return redirect()->route('quotations.index')
            ->with('success', 'Proposta criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation): Response
    {
        $quotation->load(['client', 'lines.product.vatRate', 'lines.supplier']);

        return Inertia::render('Quotations/Show', [
            'quotation' => $quotation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation): Response
    {
        $quotation->load(['client', 'lines.product.vatRate', 'lines.supplier']);

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

        $quotationData = $quotation->toArray();
        if ($quotation->quotation_date) {
            $quotationData['quotation_date'] = $quotation->quotation_date->format('Y-m-d');
        }
        if ($quotation->validity) {
            $quotationData['validity'] = $quotation->validity->format('Y-m-d');
        }

        return Inertia::render('Quotations/Edit', [
            'quotation' => $quotationData,
            'clients' => $clients,
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        $quotation->update([
            'quotation_date' => $request->quotation_date,
            'client_id' => $request->client_id,
            'validity' => $request->validity,
            'status' => $request->status,
        ]);

        $quotation->lines()->delete();

        foreach ($request->lines as $line) {
            $quotation->lines()->create([
                'product_id' => $line['product_id'],
                'supplier_id' => $line['supplier_id'] ?? null,
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'cost_price' => $line['cost_price'] ?? null,
            ]);
        }

        return redirect()->route('quotations.index')
            ->with('success', 'Proposta atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation): RedirectResponse
    {
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Proposta eliminada com sucesso');
    }

    /**
     * Generate and download PDF for the specified quotation.
     */
    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client.country', 'lines.product.vatRate', 'lines.supplier']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotations.pdf', [
            'quotation' => $quotation,
        ]);

        $filename = 'proposta-' . str_replace(['/', '\\'], '-', $quotation->number) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Convert a closed quotation to an order.
     */
    public function convertToOrder(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status !== 'closed') {
            return redirect()->route('quotations.index')
                ->with('error', 'Apenas propostas fechadas podem ser convertidas em encomendas');
        }

        return redirect()->route('quotations.index')
            ->with('success', 'Funcionalidade de conversão para encomenda será implementada em breve');
    }
}
