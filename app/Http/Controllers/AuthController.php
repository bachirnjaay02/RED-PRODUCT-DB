<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ✅ Méthode centrale pour envoyer un email via l'API Brevo
    private function sendBrevoEmail($toEmail, $toName, $subject, $htmlContent)
    {
        $apiKey = env('BREVO_API_KEY');

        $response = Http::withHeaders([
            'api-key'      => $apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name'  => env('MAIL_FROM_NAME', 'RED PRODUCT'),
                'email' => env('MAIL_FROM_ADDRESS', 'bachirndiaye233@gmail.com'),
            ],
            'to' => [[
                'email' => $toEmail,
                'name'  => $toName,
            ]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Brevo API error: ' . $response->body());
        }

        return $response->json();
    }

    // ✅ Template email activation
    private function getActivationEmailHtml($name, $code)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0;">
          <div style="max-width: 500px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.1);">
            <div style="background: #1a1a1a; padding: 30px; text-align: center;">
              <h1 style="color: white; margin: 0; font-size: 22px; letter-spacing: 3px;">RED PRODUCT</h1>
            </div>
            <div style="padding: 40px 30px; text-align: center;">
              <h2 style="color: #1a1a1a;">Bonjour ' . $name . ' 👋</h2>
              <p style="color: #555; line-height: 1.6;">Merci de vous être inscrit. Voici votre code de validation :</p>
              <div style="display: inline-block; margin: 25px auto; padding: 18px 40px; background: #f8f8f8; border: 2px dashed #1a1a1a; border-radius: 10px;">
                <div style="font-size: 42px; font-weight: bold; color: #1a1a1a; letter-spacing: 10px;">' . $code . '</div>
              </div>
              <p style="color: #555;">Entrez ce code dans l\'application pour activer votre compte.</p>
              <p style="font-size: 13px; color: #999;">Ce code est valable <strong>24 heures</strong>.<br>Si vous n\'avez pas créé de compte, ignorez cet email.</p>
            </div>
            <div style="background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #999;">
              © ' . date('Y') . ' RED PRODUCT — Tous droits réservés
            </div>
          </div>
        </body>
        </html>';
    }

    // ✅ Template email reset password
    private function getResetEmailHtml($name, $code)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0;">
          <div style="max-width: 500px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.1);">
            <div style="background: #1a1a1a; padding: 30px; text-align: center;">
              <h1 style="color: white; margin: 0; font-size: 22px; letter-spacing: 3px;">RED PRODUCT</h1>
            </div>
            <div style="padding: 40px 30px; text-align: center;">
              <h2 style="color: #1a1a1a;">Réinitialisation 🔐</h2>
              <p style="color: #555; line-height: 1.6;">Voici votre code de réinitialisation de mot de passe :</p>
              <div style="display: inline-block; margin: 25px auto; padding: 18px 40px; background: #fff3f3; border: 2px dashed #c0392b; border-radius: 10px;">
                <div style="font-size: 42px; font-weight: bold; color: #c0392b; letter-spacing: 10px;">' . $code . '</div>
              </div>
              <p style="color: #555;">Entrez ce code dans l\'application pour choisir un nouveau mot de passe.</p>
              <p style="font-size: 13px; color: #999;">Ce code expire dans <strong>60 minutes</strong>.<br>Si vous n\'avez pas fait cette demande, ignorez cet email.</p>
            </div>
            <div style="background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #999;">
              © ' . date('Y') . ' RED PRODUCT — Tous droits réservés
            </div>
          </div>
        </body>
        </html>';
    }

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

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'activation_token' => $code,
            'is_active'        => false,
        ]);

        try {
            $this->sendBrevoEmail(
                $user->email,
                $user->name,
                'Votre code de validation - RED PRODUCT',
                $this->getActivationEmailHtml($user->name, $code)
            );
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

        $user->is_active        = true;
        $user->activation_token = null;
        $user->save();

        return response()->json([
            'message' => 'Compte activé avec succès ! Vous pouvez maintenant vous connecter.'
        ]);
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
                'message'          => 'Votre compte n\'est pas encore activé. Vérifiez votre email.',
                'email'            => $user->email,
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
            $this->sendBrevoEmail(
                $user->email,
                $user->name,
                'Code de réinitialisation - RED PRODUCT',
                $this->getResetEmailHtml($user->name, $code)
            );
        } catch (\Exception $e) {
            return response()->json([
                'message'      => 'Email non envoyé.',
                'error_detail' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Code de réinitialisation envoyé par email !'
        ]);
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