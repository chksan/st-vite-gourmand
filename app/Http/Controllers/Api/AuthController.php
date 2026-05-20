<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordFacade;

class AuthController extends Controller
{

    private function validateDeliveryAddress(string $address): void
    {
        $response = Http::withHeaders([
            'User-Agent' => 'ViteGourmand-ECF/1.0'
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q'      => $address,
            'format' => 'json',
            'limit'  => 1
        ]);

        if (!$response->successful()) {
            throw new \Exception("Une erreur s'est produite lors de la vérification de votre adresse.");
        }

        $data = $response->json();

        if (count($data) === 0) {
            throw new \Exception("L'adresse saisie n'a pas pu être validée. Veuillez entrer une adresse réelle.");
        }
    }
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


        try {
            $this->validateDeliveryAddress($request->address);
        } catch (\Exception $e) {
            return response()->json(['message' => "Votre adresse n'est pas valide."], 422);
        }

        $user = Auth::user();

        $user->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'user'    => $user->fresh(),
            'message' => 'Informations personnelles mises à jour avec succès'
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'Veuillez entrer une adresse email valide.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Si cet email est associé à un compte, vous recevrez un lien de réinitialisation.'
            ], 200);
        }

        $token = app('auth.password.broker')->createToken($user);
        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($request->email));

        Mail::to($request->email)->send(new ResetPasswordMail($resetUrl, $request->email));

        return response()->json([
            'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'token.required'              => 'Le token est obligatoire.',
            'email.required'              => 'L\'email est obligatoire.',
            'email.email'                 => 'Veuillez entrer une adresse email valide.',
            'password.required'           => 'Le mot de passe est obligatoire.',
            'password.min'                => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'          => 'Les mots de passe ne correspondent pas.',
            'password.mixed_case'         => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers'            => 'Le mot de passe doit contenir au moins un chiffre.',
        ]);

        $status = PasswordFacade::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        if ($status === PasswordFacade::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Votre mot de passe a été réinitialisé avec succès.'
            ]);
        }

        $errorMessage = match ($status) {
            PasswordFacade::INVALID_TOKEN     => 'Le lien de réinitialisation est invalide ou a expiré.',
            PasswordFacade::INVALID_USER      => 'Aucun compte trouvé avec cet email.',
            default                           => 'Une erreur est survenue lors de la réinitialisation.',
        };

        return response()->json([
            'message' => $errorMessage
        ], 400);
    }    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        if (!Auth::user()->is_active) {
            Auth::logout();
            return response()->json(['message' => 'Votre compte a été désactivé.'], 403);
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
            'email'    => 'required|email|unique:users,email',
            'phone'    => ['nullable', 'string', 'regex:/^0[1-9]([-. ]?[0-9]{2}){4}$/'],
            'address'  => 'required|string|min:10|max:500',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'name.required'        => 'Le nom complet est obligatoire.',
            'name.max'             => 'Le nom est trop long (maximum 255 caractères).',
            'email.required'       => 'L\'adresse email est obligatoire.',
            'email.email'          => 'Veuillez entrer une adresse email valide.',
            'email.unique'         => 'Cet email est déjà utilisé.',
            'phone.regex'          => 'Le numéro de téléphone doit être un numéro français valide (ex: 06 XX XX XX XX).',
            'address.required'     => 'L\'adresse postale est obligatoire.',
            'address.min'          => 'L\'adresse doit contenir au moins 10 caractères.',
            'address.max'          => 'L\'adresse est trop longue (maximum 500 caractères).',
            'password.required'    => 'Le mot de passe est obligatoire.',
            'password.confirmed'   => 'Les deux mots de passe ne correspondent pas.',
            'password.min'         => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixed_case'  => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers'     => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols'     => 'Le mot de passe doit contenir au moins un caractère spécial (@, #, $, etc.).',
        ]);

        try {
            $this->validateDeliveryAddress($request->address);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "L'adresse fournie n'est pas valide."
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'address'  => $request->address,
            'role'     => 'user',
        ]);

        Mail::to($user->email)->send(new WelcomeUser($user));
        Auth::login($user);

        return response()->json([
            'user'    => $user,
            'message' => 'Compte créé avec succès !'
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