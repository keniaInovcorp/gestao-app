<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionGroupRequest;
use App\Http\Requests\UpdatePermissionGroupRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionGroupController extends Controller
{
    /**
     * Display a listing of the permission groups.
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->checkPermission('permission-groups.read');

        $permissionGroups = Role::whereNotIn('name', ['admin', 'regular'])
            ->where('guard_name', 'web')
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'status' => $role->status ?? 'active',
                    'users_count' => $role->users_count,
                ];
            });

        return Inertia::render('PermissionGroups/Index', [
            'permissionGroups' => $permissionGroups,
        ]);
    }

    /**
     * Show the form for creating a new permission group.
     *
     * @return Response
     */
    public function create(): Response
    {
        $this->checkPermission('permission-groups.create');

        $menus = $this->getMenus();
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0];
            });

        return Inertia::render('PermissionGroups/Create', [
            'menus' => $menus,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created permission group in storage.
     *
     * @param StorePermissionGroupRequest $request
     * @return RedirectResponse
     */
    public function store(StorePermissionGroupRequest $request): RedirectResponse
    {
        $this->checkPermission('permission-groups.create');

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'status' => $request->status,
        ]);

        $permissions = [];
        if (is_array($request->permissions)) {
            foreach ($request->permissions as $permissionName => $enabled) {
                if ($enabled === true || $enabled === '1' || $enabled === 1) {
                    $permission = Permission::where('name', $permissionName)
                        ->where('guard_name', 'web')
                        ->first();
                    if ($permission) {
                        $permissions[] = $permission;
                    }
                }
            }
        }

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permission-groups.index')
            ->with('success', 'Grupo de permissões criado com sucesso.');
    }

    /**
     * Display the specified permission group.
     *
     * @param Role $permissionGroup
     * @return Response
     */
    public function show(Role $permissionGroup): Response
    {
        $this->checkPermission('permission-groups.read');

        if (in_array($permissionGroup->name, ['admin', 'regular'])) {
            abort(403, 'Não pode visualizar grupos de sistema.');
        }

        $permissionGroup->load(['permissions', 'users']);

        return Inertia::render('PermissionGroups/Show', [
            'permissionGroup' => [
                'id' => $permissionGroup->id,
                'name' => $permissionGroup->name,
                'status' => $permissionGroup->status ?? 'active',
                'users_count' => $permissionGroup->users->count(),
                'permissions' => $permissionGroup->permissions->pluck('name')->toArray(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified permission group.
     *
     * @param Role $permissionGroup
     * @return Response
     */
    public function edit(Role $permissionGroup): Response
    {
        $this->checkPermission('permission-groups.update');

        if (in_array($permissionGroup->name, ['admin', 'regular'])) {
            abort(403, 'Não pode editar grupos de sistema.');
        }

        $menus = $this->getMenus();
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0];
            });

        $permissionGroup->load('permissions');
        $rolePermissions = $permissionGroup->permissions->pluck('name')->toArray();

        return Inertia::render('PermissionGroups/Edit', [
            'permissionGroup' => [
                'id' => $permissionGroup->id,
                'name' => $permissionGroup->name,
                'status' => $permissionGroup->status ?? 'active',
            ],
            'menus' => $menus,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified permission group in storage.
     *
     * @param UpdatePermissionGroupRequest $request
     * @param Role $permissionGroup
     * @return RedirectResponse
     */
    public function update(UpdatePermissionGroupRequest $request, Role $permissionGroup): RedirectResponse
    {
        $this->checkPermission('permission-groups.update');

        if (in_array($permissionGroup->name, ['admin', 'regular'])) {
            abort(403, 'Não pode editar grupos de sistema.');
        }

        $permissionGroup->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        $permissions = [];
        if (is_array($request->permissions)) {
            foreach ($request->permissions as $permissionName => $enabled) {
                if ($enabled === true || $enabled === '1' || $enabled === 1) {
                    $permission = Permission::where('name', $permissionName)
                        ->where('guard_name', 'web')
                        ->first();
                    if ($permission) {
                        $permissions[] = $permission;
                    }
                }
            }
        }

        $permissionGroup->syncPermissions($permissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permission-groups.index')
            ->with('success', 'Grupo de permissões atualizado com sucesso.');
    }

    /**
     * Remove the specified permission group from storage.
     *
     * @param mixed $permissionGroup
     * @return RedirectResponse
     */
    public function destroy($permissionGroup): RedirectResponse
    {
        $this->checkPermission('permission-groups.delete');

        if (is_numeric($permissionGroup)) {
            $permissionGroup = Role::findOrFail($permissionGroup);
        }

        if (!$permissionGroup instanceof Role) {
            abort(404, 'Grupo de permissões não encontrado.');
        }

        if (in_array($permissionGroup->name, ['admin', 'regular'])) {
            abort(403, 'Não pode eliminar grupos de sistema.');
        }

        $usersCount = $permissionGroup->users()->count();
        if ($usersCount > 0) {
            return redirect()->route('permission-groups.index')
                ->with('error', "Não pode eliminar um grupo de permissões que tem {$usersCount} utilizador(es) associado(s). Remova primeiro o grupo dos utilizadores.");
        }

        $permissionGroup->delete();

        return redirect()->route('permission-groups.index')
            ->with('success', 'Grupo de permissões eliminado com sucesso.');
    }

    /**
     * Get the list of menus with their Portuguese translations.
     *
     * @return array
     */
    private function getMenus(): array
    {
        return [
            'clients' => 'Clientes',
            'suppliers' => 'Fornecedores',
            'contacts' => 'Contactos',
            'products' => 'Artigos',
            'quotations' => 'Propostas',
            'calendar-events' => 'Calendário',
            'orders' => 'Encomendas - Clientes',
            'supplier-orders' => 'Encomendas - Fornecedores',
            'supplier-invoices' => 'Faturas Fornecedor',
            'calendar-types' => 'Calendário - Tipos',
            'calendar-actions' => 'Calendário - Acções',
            'users' => 'Utilizadores',
            'permission-groups' => 'Permissões',
            'logs' => 'Logs',
        ];
    }
}
