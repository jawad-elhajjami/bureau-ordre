<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ENS Smart Doc</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    
     {{-- The navbar with `sticky` and `full-width` --}}
     <x-mary-nav sticky full-width>
 
        <x-slot:brand>
            {{-- Drawer toggle for "main-drawer" --}}
            <label for="main-drawer" class="lg:hidden mr-3">
                {{-- <x-icon name="o-pencil" class="cursor-pointer" /> --}}
            </label>
 
            {{-- Brand --}}
            <div>App</div>
        </x-slot:brand>
 
        {{-- Right side actions --}}
        <x-slot:actions>
            <x-mary-button label="Messages" icon="o-envelope" link="###" class="btn-ghost btn-sm" responsive />
            <x-mary-button label="Notifications" icon="o-bell" link="###" class="btn-ghost btn-sm" responsive />
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
                        <x-mary-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" />
                    </x-slot:actions>
                </x-mary-list-item>
 
                <x-mary-menu-separator />
            @endif
 
            {{-- Activates the menu item when a route matches the `link` property --}}
            <x-mary-menu activate-by-route>
                <x-mary-menu-item title="Home" icon="o-home" link="###" />
                <x-mary-menu-item title="Messages" icon="o-envelope" link="###" />
                <x-mary-menu-sub title="Settings" icon="o-cog-6-tooth">
                    <x-mary-menu-item title="Wifi" icon="o-wifi" link="####" />
                    <x-mary-menu-item title="Archives" icon="o-archive-box" link="####" />
                </x-mary-menu-sub>
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
</html>