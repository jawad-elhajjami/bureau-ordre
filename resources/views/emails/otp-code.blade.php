<!DOCTYPE html>
<html>
    <head>
        <title>Code OTP</title>
    </head>
    <body>
        <h1>Code OTP pour l'Accès aux Documents</h1>
        <p>Bonjour {{ $user->name }},</p>
        <p>Votre code OTP est: <strong>{{ $otpCode }}</strong></p>
        <p>Veuillez utiliser ce code pour accéder au document en toute sécurité.</p>
        <p>Merci!</p>
    </body>
</html>
