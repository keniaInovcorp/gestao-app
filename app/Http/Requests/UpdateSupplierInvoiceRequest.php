<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,paid'],
            'payment_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'send_payment_proof' => ['nullable'],
        ];
    }
    
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $supplierInvoice = $this->route('supplierInvoice');
            if ($this->input('status') === 'paid' && !$this->hasFile('payment_proof') && !$supplierInvoice->payment_proof) {
                $validator->errors()->add('payment_proof', 'Para marcar a fatura como "Paga", é necessário fazer upload do comprovativo de pagamento.');
            }
        });
    }
}
