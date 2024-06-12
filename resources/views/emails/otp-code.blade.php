@component('mail::message')
# Code OTP pour l'Accès aux Documents

Bonjour {{ $user->name }},

Votre code OTP de document **{{ $mailSubject }}** est: **{{ $otpCode }}**

Veuillez utiliser ce code pour accéder au document en toute sécurité.
Merci!
@endcomponent
