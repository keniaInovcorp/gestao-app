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
    /**
     * Show the form for editing the user profile.
     *
     * @return Response
     */
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
            'twoFactorEnabled' => !is_null($user->two_factor_confirmed_at),
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param Request $request
     * @param UpdateUserProfileInformation $updater
     * @return RedirectResponse
     */
    public function updateProfile(Request $request, UpdateUserProfileInformation $updater): RedirectResponse
    {
        $user = Auth::user();
        
        try {
            $updater->update($user, $request->all());
            
            return redirect()->route('profile.edit')
                ->with('success', 'Perfil atualizado com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('profile.edit')
                ->withErrors($e->errors(), 'updateProfileInformation')
                ->withInput();
        }
    }

    /**
     * Update the user's password.
     *
     * @param Request $request
     * @param UpdateUserPassword $updater
     * @return RedirectResponse
     */
    public function updatePassword(Request $request, UpdateUserPassword $updater): RedirectResponse
    {
        $user = Auth::user();
        
        $updater->update($user, $request->all());

        return redirect()->route('profile.edit')
            ->with('success', 'Password atualizada com sucesso');
    }
}

