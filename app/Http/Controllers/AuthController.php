<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'name.required'     => 'Le nom est obligatoire.',
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email n\'est pas valide.',
            'email.unique'      => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // ✅ Générer un code à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'activation_token' => $code,
            'is_active'        => false,
        ]);

        try {
            Mail::send('emails.activation', [
                'code' => $code,
                'name' => $user->name
            ], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Votre code de validation - RED PRODUCT');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message'      => 'Inscription réussie mais email non envoyé. Contactez le support.',
                'warning'      => true,
                'error_detail' => $e->getMessage()
            ], 201);
        }

        return response()->json([
            'message' => 'Inscription réussie ! Entrez le code reçu par email pour activer votre compte.',
            'email'   => $user->email
        ], 201);
    }

    // ✅ Nouvelle méthode — vérification du code
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('activation_token', $request->code)
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 400);
        }

        $user->is_active = true;
        $user->activation_token = null;
        $user->save();

        return response()->json(['message' => 'Compte activé avec succès ! Vous pouvez maintenant vous connecter.']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants invalides.'], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Votre compte n\'est pas encore activé. Vérifiez votre email.',
                'email'   => $user->email,
                'needs_activation' => true
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Connexion réussie',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email non trouvé.'], 404);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $code, 'created_at' => now()]
        );

        try {
            Mail::send('emails.reset', [
                'code' => $code,
                'name' => $user->name
            ], function ($m) use ($user) {
                $m->to($user->email)->subject('Code de réinitialisation - RED PRODUCT');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message'      => 'Email non envoyé.',
                'error_detail' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Code de réinitialisation envoyé par email !']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'code'     => 'required|string|size:6',
            'password' => 'required|min:6'
        ]);

        $record = DB::table('password_resets')
                    ->where('email', $request->email)
                    ->where('token', $request->code)
                    ->first();

        if (!$record) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès !']);
    }
}