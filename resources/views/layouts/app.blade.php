<!DOCTYPE html>
<html data-theme="" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Bureau d\'ordre') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

        {{-- The navbar with `sticky` and `full-width` --}}
        <x-mary-nav sticky full-width>

            <x-slot:brand>
                {{-- Drawer toggle for "main-drawer" --}}
                <label for="main-drawer" class="lg:hidden mr-3">
                    <x-mary-icon name="o-bars-3" class="cursor-pointer" />
                </label>

                {{-- Brand --}}
                <img src="{{ asset('images/logo.png') }}" alt="ENS Smart Doc Logo PNG" width="60px">
            </x-slot:brand>

            {{-- Right side actions --}}
            <x-slot:actions>
                <x-mary-button label="Messages reçues" icon="o-envelope" link="###" class="btn-ghost btn-sm" responsive />
                <x-mary-dropdown>
                    <x-slot:trigger>
                        <x-mary-button icon="o-bell" label="Notifications" class="btn-ghost btn-sm" responsive />
                    </x-slot:trigger>
                 
                    <x-mary-menu-item title="Notification 1" />
                    <x-mary-menu-item title="Notification 2" />
                </x-mary-dropdown>
                <x-mary-dropdown>
                    <x-slot:trigger>
                        <x-mary-button icon="o-computer-desktop" label="Changez le theme" class="btn-ghost btn-sm" responsive />
                    </x-slot:trigger>
                 
                    <x-mary-menu-item title="Emerald" onclick="changeTheme('emerald')" />
                    <x-mary-menu-item title="Light" onclick="changeTheme('light')" />
                    <x-mary-menu-item title="Winter" onclick="changeTheme('winter')" />
                    <x-mary-menu-item title="Lofi" onclick="changeTheme('lofi')" />
                    <x-mary-menu-item title="Nord" onclick="changeTheme('nord')" />

                </x-mary-dropdown>
            </x-slot:actions>
        </x-mary-nav>

        {{-- The main content with `full-width` --}}
        <x-mary-main with-nav full-width>

            {{-- This is a sidebar that works also as a drawer on small screens --}}
            {{-- Notice the `main-drawer` reference here --}}
            <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200">

                {{-- User --}}
                @if($user = auth()->user())
                    <x-mary-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="pt-2">
                        <x-slot:actions>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-nav-link href="{{ route('logout') }}"
                                               @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-nav-link>
                            </form>
                        </x-slot:actions>
                    </x-mary-list-item>

                    <x-mary-menu-separator />
                @endif

                {{-- Activates the menu item when a route matches the `link` property --}}
                <x-mary-menu activate-by-route>
                    <x-mary-menu-item title="Tableau de bord" icon="o-home" link="{{ route('dashboard') }}" wire:navigate/>
                    <x-mary-menu-item title="Documents" icon="o-document" link="###" />
                    @if(Auth::user() && Auth::user()->role->name == 'admin')
                        <x-mary-menu-item title="Gérer les utilisateurs" icon="o-user" link="{{ route('manage-users') }}" wire:navigate/>
                        <x-mary-menu-item title="Services" icon="o-building-office" link="{{ route('manage-services') }}" wire:navigate/>
                        <x-mary-menu-item title="Catégories de documents" icon="o-folder" link="{{ route('manage-categories') }}" wire:navigate />
                    @endif
                    <x-mary-menu-item title="Paramètres" icon="o-cog-6-tooth" link="{{ route('profile.show') }}" />
                </x-mary-menu>
            </x-slot:sidebar>

            {{-- The `$slot` goes here --}}
            <x-slot:content>
                {{ $slot }}
            </x-slot:content>
        </x-mary-main>

        {{--  TOAST area --}}
        <x-mary-toast />
    </body>

        @stack('modals')

        @livewireScripts
        
        <script>
            function changeTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
            }
        </script>
    </body>
</html>
