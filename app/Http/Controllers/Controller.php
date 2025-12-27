<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function checkPermission(string $permission): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Acesso negado. Deve estar autenticado.');
        }
        
        if ($user->hasRole('admin')) {
            return;
        }
        
        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'Acesso negado. Não tem permissão para esta ação.');
        }
    }
}
