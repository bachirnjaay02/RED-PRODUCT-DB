<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 500px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
    .header { background: #1a1a1a; padding: 30px; text-align: center; }
    .header h1 { color: white; margin: 0; font-size: 22px; letter-spacing: 3px; }
    .body { padding: 40px 30px; text-align: center; }
    .body h2 { color: #1a1a1a; margin-bottom: 10px; }
    .body p { color: #555; line-height: 1.6; }
    .code-box { display: inline-block; margin: 25px auto; padding: 18px 40px; background: #fff3f3; border: 2px dashed #c0392b; border-radius: 10px; }
    .code { font-size: 42px; font-weight: bold; color: #c0392b; letter-spacing: 10px; }
    .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #999; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>RED PRODUCT</h1>
    </div>
    <div class="body">
      <h2>Réinitialisation 🔐</h2>
      <p>Voici votre code de réinitialisation de mot de passe :</p>
      <div class="code-box">
        <div class="code">{{ $code }}</div>
      </div>
      <p>Entrez ce code dans l'application pour choisir un nouveau mot de passe.</p>
      <p style="font-size: 13px; color: #999;">Ce code expire dans <strong>60 minutes</strong>.<br>Si vous n'avez pas fait cette demande, ignorez cet email.</p>
    </div>
    <div class="footer">© {{ date('Y') }} RED PRODUCT — Tous droits réservés</div>
  </div>
</body>
</html>