<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierInvoiceRequest;
use App\Http\Requests\UpdateSupplierInvoiceRequest;
use App\Models\SupplierInvoice;
use App\Models\Entity;
use App\Models\SupplierOrder;
use App\Mail\SupplierInvoicePaymentProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class SupplierInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->checkPermission('supplier-invoices.read');

        $supplierInvoices = SupplierInvoice::with(['supplier', 'supplierOrder'])
            ->orderBy('invoice_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(15);

        return Inertia::render('SupplierInvoices/Index', [
            'supplierInvoices' => $supplierInvoices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->checkPermission('supplier-invoices.create');

        $suppliers = Entity::where('type', 'supplier')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $supplierOrders = SupplierOrder::where('status', 'closed')
            ->with('supplier')
            ->orderBy('order_date', 'desc')
            ->get();

        return Inertia::render('SupplierInvoices/Create', [
            'suppliers' => $suppliers,
            'supplierOrders' => $supplierOrders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierInvoiceRequest $request): RedirectResponse
    {
        $this->checkPermission('supplier-invoices.create');

        $lastInvoice = SupplierInvoice::orderBy('number', 'desc')->first();
        $year = Carbon::now()->format('Y');
        
        if ($lastInvoice && str_starts_with($lastInvoice->number, $year)) {
            $lastNumber = (int) substr($lastInvoice->number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        
        $number = $year . '/' . $nextNumber;

        $data = $request->validated();
        $data['number'] = $number;

        if ($request->hasFile('document')) {
            $document = $request->file('document');
            $filename = time() . '_' . uniqid() . '.' . $document->getClientOriginalExtension();
            $path = $document->storeAs('faturas-fornecedor', $filename, 'private');
            $data['document'] = $path;
        }

        if ($request->hasFile('payment_proof')) {
            $paymentProof = $request->file('payment_proof');
            $filename = time() . '_' . uniqid() . '.' . $paymentProof->getClientOriginalExtension();
            $path = $paymentProof->storeAs('faturas-fornecedor', $filename, 'private');
            $data['payment_proof'] = $path;
        }

        $invoice = SupplierInvoice::create($data);
        $this->logActivity($invoice, 'created', 'supplier-invoice', $request);

        return redirect()->route('supplier-invoices.index')
            ->with('success', 'Fatura criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierInvoice $supplierInvoice): Response
    {
        $this->checkPermission('supplier-invoices.read');

        $supplierInvoice->load(['supplier', 'supplierOrder']);
        $this->logActivity($supplierInvoice, 'viewed', 'supplier-invoice', request());

        return Inertia::render('SupplierInvoices/Show', [
            'supplierInvoice' => $supplierInvoice,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierInvoice $supplierInvoice): Response|RedirectResponse
    {
        if (!$supplierInvoice->document) {
            return redirect()->route('supplier-invoices.index')
                ->with('error', 'Esta fatura ainda não tem documento. Apenas faturas com documento podem ser editadas.');
        }

        $supplierInvoice->load(['supplier', 'supplierOrder']);

        $invoiceData = $supplierInvoice->toArray();
        if ($supplierInvoice->invoice_date) {
            $invoiceData['invoice_date'] = $supplierInvoice->invoice_date->format('Y-m-d');
        }
        if ($supplierInvoice->due_date) {
            $invoiceData['due_date'] = $supplierInvoice->due_date->format('Y-m-d');
        }

        return Inertia::render('SupplierInvoices/Edit', [
            'supplierInvoice' => $invoiceData,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierInvoiceRequest $request, SupplierInvoice $supplierInvoice): RedirectResponse
    {
        $this->checkPermission('supplier-invoices.update');

        $oldStatus = $supplierInvoice->status;
        
        $validated = $request->validated();
        $data = [
            'status' => $validated['status'],
        ];

        if ($request->hasFile('payment_proof')) {
            if ($supplierInvoice->payment_proof && Storage::disk('private')->exists($supplierInvoice->payment_proof)) {
                Storage::disk('private')->delete($supplierInvoice->payment_proof);
            }
            $paymentProof = $request->file('payment_proof');
            $filename = time() . '_' . uniqid() . '.' . $paymentProof->getClientOriginalExtension();
            $path = $paymentProof->storeAs('faturas-fornecedor', $filename, 'private');
            $data['payment_proof'] = $path;
        }

        $supplierInvoice->update($data);
        $this->logActivity($supplierInvoice, 'updated', 'supplier-invoice', $request);

        $supplierInvoice->refresh();

        if ($oldStatus === 'pending' && $supplierInvoice->status === 'paid' && $request->input('send_payment_proof') && $supplierInvoice->payment_proof) {
            $this->sendPaymentProofEmail($supplierInvoice);
            return redirect()->route('supplier-invoices.index')
                ->with('success', 'Fatura atualizada com sucesso. Email enviado ao fornecedor.');
        }

        return redirect()->route('supplier-invoices.index')
            ->with('success', 'Fatura atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierInvoice $supplierInvoice): RedirectResponse
    {
        $this->checkPermission('supplier-invoices.delete');

        if ($supplierInvoice->document && Storage::disk('private')->exists($supplierInvoice->document)) {
            Storage::disk('private')->delete($supplierInvoice->document);
        }
        if ($supplierInvoice->payment_proof && Storage::disk('private')->exists($supplierInvoice->payment_proof)) {
            Storage::disk('private')->delete($supplierInvoice->payment_proof);
        }

        $invoiceNumber = $supplierInvoice->number;
        $supplierInvoice->delete();
        $this->logActivity(null, 'deleted', 'supplier-invoice', request(), "Deleted supplier-invoice {$invoiceNumber}");

        return redirect()->route('supplier-invoices.index')
            ->with('success', 'Fatura eliminada com sucesso');
    }

    /**
     * Download the invoice document.
     */
    public function downloadDocument(SupplierInvoice $supplierInvoice)
    {
        if (!$supplierInvoice->document || !Storage::disk('private')->exists($supplierInvoice->document)) {
            abort(404, 'Documento não encontrado');
        }

        return response()->download(Storage::disk('private')->path($supplierInvoice->document));
    }

    /**
     * Download the payment proof.
     */
    public function downloadPaymentProof(SupplierInvoice $supplierInvoice)
    {
        if (!$supplierInvoice->payment_proof || !Storage::disk('private')->exists($supplierInvoice->payment_proof)) {
            abort(404, 'Comprovativo não encontrado');
        }

        return response()->download(Storage::disk('private')->path($supplierInvoice->payment_proof));
    }

    /**
     * Send payment proof email to supplier.
     */
    private function sendPaymentProofEmail(SupplierInvoice $supplierInvoice): void
    {
        $supplierInvoice->load('supplier');
        
        if (!$supplierInvoice->supplier || !$supplierInvoice->payment_proof) {
            return;
        }

        try {
            $supplierEmail = Crypt::decryptString($supplierInvoice->supplier->email);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $supplierEmail = $supplierInvoice->supplier->email;
        }
        
        if (!$supplierEmail) {
            return;
        }
        
        Mail::to($supplierEmail)->send(new SupplierInvoicePaymentProof($supplierInvoice));
    }
}
