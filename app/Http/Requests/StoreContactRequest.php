<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'entity_id' => ['required', 'exists:entities,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'function_id' => ['required', 'exists:contact_functions,id'],
            'phone' => ['nullable', 'string'],
            'mobile' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'gdpr_consent' => ['required', 'boolean', 'accepted'],
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
            'entity_id.required' => 'A entidade é obrigatória.',
            'entity_id.exists' => 'A entidade selecionada é inválida.',
            'first_name.required' => 'O nome é obrigatório.',
            'last_name.required' => 'O apelido é obrigatório.',
            'function_id.required' => 'A função é obrigatória.',
            'function_id.exists' => 'A função selecionada é inválida.',
            'email.email' => 'O formato do email é inválido.',
            'gdpr_consent.required' => 'É obrigatório aceitar o consentimento RGPD.',
            'gdpr_consent.accepted' => 'É obrigatório aceitar o consentimento RGPD.',
            'status.required' => 'O estado é obrigatório.',
            'status.in' => 'O estado selecionado é inválido.',
        ];
    }
}
