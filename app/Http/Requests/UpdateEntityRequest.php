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
            'tax_number' => [
                'required',
                'string',
                Rule::unique('entities')
                    ->where(function ($query) {
                        return $query->where('type', $this->type);
                    })
                    ->ignore($this->entity),
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'postal_code' => ['required', 'regex:/^\d{4}-\d{3}$/'],
            'city' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'phone' => ['nullable', 'string'],
            'mobile' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'email' => ['nullable', 'email'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tax_number.unique' => 'Este NIF já está registado como ' . ($this->type === 'client' ? 'cliente' : 'fornecedor') . '.',
            'tax_number.required' => 'O NIF é obrigatório.',
            'name.required' => 'O nome é obrigatório.',
            'address.required' => 'A morada é obrigatória.',
            'postal_code.required' => 'O código postal é obrigatório.',
            'postal_code.regex' => 'O formato do código postal é inválido (XXXX-XXX).',
            'city.required' => 'A localidade é obrigatória.',
            'country_id.required' => 'O país é obrigatório.',
            'country_id.exists' => 'O país selecionado é inválido.',
            'website.url' => 'O formato do website é inválido.',
            'email.email' => 'O formato do email é inválido.',
            'status.required' => 'O estado é obrigatório.',
            'status.in' => 'O estado selecionado é inválido.',
        ];
    }
}
