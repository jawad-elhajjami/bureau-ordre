<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent dark:border-gray-700">
    @if(Auth::user()->role->name == 'admin')
        <p>{{ __("Welcome Admin") }}</p>
    @elseif(Auth::user()->role->name == 'user')
        <p>{{ __("Welcome User") }}</p>
    @endif
</div>
