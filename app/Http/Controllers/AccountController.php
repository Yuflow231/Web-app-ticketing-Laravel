<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Chercher l'utilisateur par email
        $user = User::where('email', $credentials['email'])->first();

        // Vérifier le mot de passe
        if ($user && Hash::check($credentials['password'], $user->password_hashed)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Afficher le formulaire de création de compte
     */
    public function showRegister()
    {
        return view('create-account');
    }

    /**
     * Alias pour showRegister (pour la route /create-account)
     */
    public function create()
    {
        return $this->showRegister();
    }

    /**
     * Créer un nouveau compte
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'language' => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password_hashed' => Hash::make($validated['password']),
            'role' => 'Guest',
            'join_date' => now(),
            'language' => $validated['language'] ?? 'en',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Afficher le profil
     */
    public function showProfile()
    {
        return view('profile', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Alias pour showProfile (pour la route /profile)
     */
    public function profile()
    {
        return $this->showProfile();
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'language' => 'nullable|string|max:10',
            'profile_pic' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profiles', 'public');
            $validated['profile_pic'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showResetPassword()
    {
        return view('reset-password');
    }

    /**
     * Alias pour showResetPassword (pour la route /reset-password)
     */
    public function password()
    {
        return $this->showResetPassword();
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password_hashed)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
        }

        $user->update([
            'password_hashed' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès');
    }
}
