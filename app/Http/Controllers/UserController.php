<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\UserPasswordSetup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function checkAdminAccess(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Acesso negado. Apenas administradores podem gerir utilizadores.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->checkAdminAccess();

        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->checkPermission('users.create');

        $roles = Role::whereIn('name', ['admin', 'regular'])
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $permissionGroups = Role::whereNotIn('name', ['admin', 'regular'])
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->checkPermission('users.create');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make(Str::random(32)),
            'status' => $request->status,
        ]);

        $rolesToAssign = [];
        
        if ($request->role_id) {
            $role = Role::findById($request->role_id);
            if ($role) {
                $rolesToAssign[] = $role;
                
                if ($request->filled('permission_group_id') && $request->permission_group_id && $role->name === 'regular') {
                    $permissionGroup = Role::findById($request->permission_group_id);
                    if ($permissionGroup) {
                        $rolesToAssign[] = $permissionGroup;
                    }
                }
            }
        }
        
        if (!empty($rolesToAssign)) {
            $user->syncRoles($rolesToAssign);
            $user->refresh();
        }

        $token = Password::createToken($user);
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

        Mail::to($user->email)->send(new UserPasswordSetup($user, $resetUrl));

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso. Email de definição de password enviado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        $this->checkPermission('users.read');

        $user->load('roles');

        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        $this->checkPermission('users.update');

        $user->load('roles');
        $roles = Role::whereIn('name', ['admin', 'regular'])
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $permissionGroups = Role::whereNotIn('name', ['admin', 'regular'])
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->checkPermission('users.update');

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status,
        ]);

        $rolesToAssign = [];
        $role = Role::findById($request->role_id);
        
        if ($role) {
            $rolesToAssign[] = $role;
            
            if ($request->filled('permission_group_id') && $request->permission_group_id && $role->name === 'regular') {
                $permissionGroup = Role::findById($request->permission_group_id);
                if ($permissionGroup) {
                    $rolesToAssign[] = $permissionGroup;
                }
            }
        }
        
        $user->syncRoles($rolesToAssign);
        $user->refresh();

        return redirect()->route('users.index')
            ->with('success', 'Utilizador atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->checkPermission('users.delete');

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Não pode eliminar o seu próprio utilizador');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilizador eliminado com sucesso');
    }
}

