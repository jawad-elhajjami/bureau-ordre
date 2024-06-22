<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body class="antialiased bg-gray-200 min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-xl p-6 md:p-12 flex items-center flex-col w-full md:w-[600px] mx-auto">
                <img src="{{ asset("/images/logo.png") }}" alt="Logo ENS Smart Doc" class="mb-4 md:mb-8 w-24 h-auto md:w-32">
                <h1 class="text-center text-2xl md:text-3xl text-gray-900 mb-4">Bienvenue sur <b>ENS SmartDoc</b></h1>
                <p class="text-center text-md text-gray-600 mb-6 md:mb-8">
                    {{ __("Découvrez notre plateforme de gestion documentaire intelligente, conçue pour simplifier votre travail et améliorer votre productivité.") }}
                </p>
                <h2 class="font-bold text-gray-500 mb-4 md:mb-8">{{ __("Ce dont vous bénéficiez de notre application") }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 w-full mb-6 md:mb-8">
                    <div class="bg-white border border-gray-300 rounded-lg p-4 text-center text-gray-600 text-sm flex items-center justify-center hover:bg-gray-200 duration-200">Gestion des Documents</div>
                    <div class="bg-white border border-gray-300 rounded-lg p-4 text-center text-gray-600 text-sm flex items-center justify-center hover:bg-gray-200 duration-200">Sécurité Renforcée</div>
                    <div class="bg-white border border-gray-300 rounded-lg p-4 text-center text-gray-600 text-sm flex items-center justify-center hover:bg-gray-200 duration-200">Rapports et Statistiques</div>
                    <div class="bg-white border border-gray-300 rounded-lg p-4 text-center text-gray-600 text-sm flex items-center justify-center hover:bg-gray-200 duration-200">Notifications en Temps Réel</div>
                    <div class="bg-white border border-gray-300 rounded-lg p-4 text-center text-gray-600 text-sm flex items-center justify-center hover:bg-gray-200 duration-200">Interface Conviviale</div>
                    <div class="bg-white border border-gray-300 rounded-lg p-4 text-center text-gray-600 text-sm flex items-center justify-center hover:bg-gray-200 duration-200">Archivage et Historique</div>
                </div>
                <x-mary-button class="btn-primary w-full" link="{{ route('login') }}">S'authentifier</x-mary-button>
            </div>
        </div> 
    </body>
</html>
