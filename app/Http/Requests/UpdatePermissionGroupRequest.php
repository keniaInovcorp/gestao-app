<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdatePermissionGroupRequest extends FormRequest
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
        $permissionGroup = $this->route('permission_group');
        $permissionGroupId = $permissionGroup instanceof Role ? $permissionGroup->id : $permissionGroup;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($permissionGroupId)],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['boolean'],
        ];
    }

}
