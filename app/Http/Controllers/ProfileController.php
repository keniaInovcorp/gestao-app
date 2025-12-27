<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = Auth::user();
        
        return Inertia::render('Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
        ]);
    }

    public function updateProfile(Request $request, UpdateUserProfileInformation $updater): RedirectResponse
    {
        $user = Auth::user();
        
        $updater->update($user, $request->all());

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil atualizado com sucesso');
    }

    public function updatePassword(Request $request, UpdateUserPassword $updater): RedirectResponse
    {
        $user = Auth::user();
        
        $updater->update($user, $request->all());

        return redirect()->route('profile.edit')
            ->with('success', 'Password atualizada com sucesso');
    }
}

