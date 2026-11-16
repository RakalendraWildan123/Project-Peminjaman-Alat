<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto_profile) {
                $oldPath = str_replace('/storage/', '', $user->foto_profile);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('foto')->store('profiles', 'public');
            $user->foto_profile = '/storage/' . $path;
        }

        $user->save();

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
