<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil.
     */
    public function show()
    {
        return view('customer.profile');
    }

    /**
     * Update nama dan email.
     */
    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update kata sandi.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.'])
                ->withInput();
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
