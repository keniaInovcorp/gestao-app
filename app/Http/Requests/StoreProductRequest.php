<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'reference' => ['required', 'string', 'max:255', 'unique:products,reference'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'vat_rate_id' => ['required', 'exists:vat_rates,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
            'reference.required' => 'A referência é obrigatória.',
            'reference.unique' => 'Esta referência já está registada.',
            'name.required' => 'O nome é obrigatório.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço não pode ser negativo.',
            'vat_rate_id.required' => 'A taxa de IVA é obrigatória.',
            'vat_rate_id.exists' => 'A taxa de IVA selecionada é inválida.',
            'photo.image' => 'O ficheiro deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou gif.',
            'photo.max' => 'A imagem não pode ter mais de 2MB.',
            'status.required' => 'O estado é obrigatório.',
            'status.in' => 'O estado selecionado é inválido.',
        ];
    }
}
