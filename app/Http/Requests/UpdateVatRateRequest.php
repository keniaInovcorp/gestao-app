<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVatRateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
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
            'name.required' => 'O nome é obrigatório.',
            'percentage.required' => 'A percentagem é obrigatória.',
            'percentage.numeric' => 'A percentagem deve ser um número.',
            'percentage.min' => 'A percentagem não pode ser negativa.',
            'percentage.max' => 'A percentagem não pode ser superior a 100%.',
            'status.required' => 'O estado é obrigatório.',
            'status.in' => 'O estado selecionado é inválido.',
        ];
    }
}
