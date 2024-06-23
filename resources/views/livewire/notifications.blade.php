<x-mary-dropdown>
    <x-slot:trigger>
        <div class="notification-indicator">
            <x-mary-button icon="o-bell" class="btn-circle btn-outline relative">
                @if($unreadCount > 0)
                    <span id="notification-indicator-dot" class="indicator-dot bg-green-500 w-3 h-3 rounded-full absolute top-0 left-0"></span>
                @endif
            </x-mary-button>
        </div>
    </x-slot:trigger>

    <div id="notification-list" class="max-h-64 overflow-y-auto custom-scrollbar" wire:click="markAsRead">
        @if($notifications->count() > 0)
            @foreach ($notifications as $notification)
                <x-mary-menu-item
                    title="{{ $notification->data['message'] }}"
                    class="notification {{ $notification->read_at ? 'read' : 'unread' }} block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                    style="{{ $notification->read_at ? '' : 'font-weight: bold;' }}"
                />
            @endforeach
        @else
            <x-mary-menu-item title="Vous n'avez pas encore de notifications" class="block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" />
        @endif
    </div>
</x-mary-dropdown>
