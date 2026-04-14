<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Génération d'un token unique pour l'activation
        $token = Str::random(64);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activation_token' => $token,
            'is_active' => false, // Compte inactif par défaut
        ]);

        // URL de ton frontend (Vercel) pour l'activation
        // Assure-toi que cette route existe dans ton App.js React
        $activationUrl = "https://red-product-client-n459.vercel.app/activate/" . $token;

        try {
            // Envoi de l'email
            Mail::send('emails.activation', ['url' => $activationUrl, 'name' => $user->name], function($message) use ($user) {
                $message->to($user->email);
                $message->subject('Activation de votre compte - RED PRODUCT');
            });
        } catch (\Exception $e) {
            // Optionnel : logger l'erreur si le mail ne part pas
            return response()->json([
                'message' => 'Utilisateur créé, mais l\'envoi de l\'email a échoué. Contactez le support.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Inscription réussie ! Veuillez vérifier vos e-mails pour activer votre compte.'
        ], 201);
    }

    /**
     * Connexion de l'utilisateur
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // 1. Vérifier si l'utilisateur existe et si le mot de passe est correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants invalides.'], 401);
        }

        // 2. Vérifier si le compte est activé
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Votre compte n\'est pas encore activé. Veuillez vérifier vos e-mails.'
            ], 403);
        }

        // 3. Si tout est OK, générer le token (exemple avec Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Méthode pour activer le compte (appelée depuis React)
     */
    public function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token d\'activation invalide ou expiré.'], 404);
        }

        // Activer le compte et vider le token
        $user->is_active = true;
        $user->activation_token = null;
        $user->save();

        return response()->json(['message' => 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.']);
    }

    public function sendResetLink(Request $request) {
    $request->validate(['email' => 'required|email']);
    $user = User::where('email', $request->email)->first();

    if (!$user) return response()->json(['message' => 'Email non trouvé.'], 404);

    $token = Str::random(64);
    DB::table('password_resets')->updateOrInsert(
        ['email' => $user->email],
        ['token' => $token, 'created_at' => now()]
    );

    $url = "https://red-product-front-mzvk.vercel.app/reset-password/" . $token;

    Mail::send('emails.reset', ['url' => $url], function($m) use($user) {
        $m->to($user->email)->subject('Réinitialisation de mot de passe');
    });

    return response()->json(['message' => 'Lien de réinitialisation envoyé par mail !']);
}

public function resetPassword(Request $request) {
    $request->validate(['token' => 'required', 'password' => 'required|min:6']);
    
    $record = DB::table('password_resets')->where('token', $request->token)->first();
    if (!$record) return response()->json(['message' => 'Token invalide.'], 400);

    $user = User::where('email', $record->email)->first();
    $user->update(['password' => Hash::make($request->password)]);

    DB::table('password_resets')->where('email', $record->email)->delete();

    return response()->json(['message' => 'Mot de passe mis à jour !']);
}
}