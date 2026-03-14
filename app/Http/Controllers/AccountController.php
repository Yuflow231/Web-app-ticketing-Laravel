<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get the user by email
        $user = User::where('email', $credentials['email'])->first();

        // Verify password
        if ($user && Hash::check($credentials['password'], $user->password_hashed)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The login details aren t valid.',
        ])->onlyInput('email');
    }

    /**
     * Log out
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show account creation form
     */
    public function showRegister()
    {
        return view('create-account');
    }

    /**
     * Create a new account
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

        return redirect()->route('dashboard');
    }

    /**
     * Show profile page
     */
    public function showProfile()
    {
        return view('profile', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the profile
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

        return back()->with('success', 'Profile updated successfully');
    }

    /**
     * Show the reset password form
     */
    public function showResetPassword()
    {
        return view('reset-password');
    }

    /**
     * Alias for showResetPassword (route /reset-password)
     */
    public function password()
    {
        return $this->showResetPassword();
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password_hashed)) {
            return back()->withErrors(['current_password' => 'Incorrect password']);
        }

        $user->update([
            'password_hashed' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    public function confirmDelete(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('password'), $user->password_hashed)) {
            return back()->withErrors(['password' => 'Incorrect password']);
        }

        return $this->destroy($request);
    }

    /**
     * Delete the authenticated user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();


        if (!empty($user->profile_pic) && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        // Get projects where this user is owner
        $ownedProjects = $user->projects()->wherePivot('role', 'Owner')->get();

        foreach ($ownedProjects as $ownedProject) {
            // Get remaining members excluding the user being deleted
            $remainingMembers = $ownedProject->teamMembers()
                ->where('users.id', '!=', $user->id)
                ->get();

            if ($remainingMembers->isEmpty()) {
                // No one left => delete the project and its contract
                if (!empty($ownedProject->contract) && Storage::disk('public')->exists($ownedProject->contract)) {
                    Storage::disk('public')->delete($ownedProject->contract);
                }
                $ownedProject->delete();
            }else{
                $firstMember = $remainingMembers->first();

                // Detach deleted user first
                $ownedProject->teamMembers()->detach($user->id);

                // Promote new owner in pivot
                $ownedProject->teamMembers()->updateExistingPivot($firstMember->id, [
                    'role' => 'Owner',
                ]);
            }
        }

        $user->delete();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
