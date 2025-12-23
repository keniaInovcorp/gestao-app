<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntityRequest extends FormRequest
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
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:client,supplier'],
            'tax_number' => ['required', 'string', Rule::unique('entities')->ignore($this->entity)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'postal_code' => ['required', 'regex:/^\d{4}-\d{3}$/'],
            'city' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'phone' => ['nullable', 'string'],
            'mobile' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'email' => ['nullable', 'email'],
            'gdpr_consent' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
