<x-mary-dropdown>
    <x-slot:trigger>
        <div class="notification-indicator">
            <x-mary-button icon="o-bell" class="btn-circle btn-outline relative">
                @if($unreadCount > 0)
                    <span id="notification-indicator-dot" class="indicator-dot bg-red-500 w-3 h-3 rounded-full absolute top-0 left-0 p-2 flex items-center justify-center text-white">{{ $unreadCount }}</span>
                @endif
            </x-mary-button>
        </div>
    </x-slot:trigger>

    <div id="notification-list" class="max-h-64 overflow-y-auto custom-scrollbar p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-medium">{{ __('messages.notifications_dropdown_title') }}</h3>
            <div class="flex gap-2">
                <x-mary-button label="{{ __('messages.mark_all_as_read_btn') }}" class="btn-ghost btn-sm" wire:click="markAsRead"/>
                <x-mary-button icon="o-trash" class="btn-error text-white btn-sm" wire:click="deleteAllNotifications"/>
            </div>
        </div>
        @if($notifications->count() > 0)
            @foreach ($notifications as $notification)
                @if(!$notification->read_at)
                    <x-mary-menu-item
                        title="{{ $notification->data['message'] }}"
                        class="notification unread block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                        style="font-weight: bold;"
                        link="{{ data_get($notification->data, 'link') }}"
                        no-wire-navigate
                        badge="{{ __('messages.new') }}"
                        badge-classes="!badge-warning"
                    />
                @else
                    <x-mary-menu-item
                        title="{{ $notification->data['message'] }}"
                        class="notification read block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                        link="{{ data_get($notification->data, 'link') }}"
                        no-wire-navigate
                    />
                @endif
            @endforeach
        @else
            <x-mary-menu-item title="Vous n'avez pas encore de notifications" class="block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" />
        @endif
    </div>
</x-mary-dropdown>
