<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateSupplierInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'status' => ['required', 'in:pending,paid'],
            'send_payment_proof' => ['nullable'],
        ];

        // Only validate payment_proof if it's actually present in the request
        if ($this->hasFile('payment_proof')) {
            $rules['payment_proof'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
        }

        return $rules;
    }
    
    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $supplierInvoice = $this->route('supplierInvoice');
            if ($this->input('status') === 'paid' && !$this->hasFile('payment_proof') && (!$supplierInvoice || !$supplierInvoice->payment_proof)) {
                $validator->errors()->add('payment_proof', 'Para marcar a fatura como "Paga", é necessário fazer upload do comprovativo de pagamento.');
            }
        });
    }
}
