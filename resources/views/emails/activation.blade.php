<!DOCTYPE html>
<html>
<head>
    <style>
        .btn { background: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Bonjour {{ $name }},</h2>
    <p>Merci de vous être inscrit sur RED PRODUCT. Cliquez sur le bouton ci-dessous pour activer votre compte :</p>
    <a href="{{ $url }}" class="btn">Activer mon compte</a>
    <p>Si le bouton ne fonctionne pas, copiez ce lien : <br> {{ $url }}</p>
</body>
</html>