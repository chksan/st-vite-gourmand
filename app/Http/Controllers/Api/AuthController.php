<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => ['nullable', 'string', 'regex:/^0[1-9]([-. ]?[0-9]{2}){4}$/'],
            'address' => 'nullable|string|min:10|max:500',
        ], [
            'phone.regex' => 'Le numéro de téléphone doit être un numéro français valide (10 chiffres commençant par 0).',
            'address.min' => 'L\'adresse doit contenir au moins 10 caractères.',
            'address.max' => 'L\'adresse est trop longue (maximum 500 caractères).',
        ]);

        $user = Auth::user();

        $user->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'user'    => $user->fresh(),
            'message' => 'Informations personnelles mises à jour avec succès'
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => Auth::user(),
            'message' => 'Connexion réussie'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => ['nullable', 'string', 'regex:/^0[1-9]([-. ]?[0-9]{2}){4}$/'],
            'address'  => 'required|string|min:10|max:500',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'phone.regex' => 'Le numéro de téléphone doit être un numéro français valide (ex: 06 XX XX XX XX).',
            'address.min' => 'L\'adresse postale doit contenir au moins 10 caractères.',
            'address.max' => 'L\'adresse est trop longue (maximum 500 caractères).',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'address'  => $request->address,
            'role'     => 'user',
        ]);

        Auth::login($user);

        return response()->json([
            'user'    => $user,
            'message' => 'Compte créé avec succès'
        ], 201);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }
}