<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class StorePermissionGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['boolean'],
        ];
    }

}
