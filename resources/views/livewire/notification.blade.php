<div>
    <x-mary-dropdown>
        <x-slot:trigger>
            <div class="notification-indicator">
                <x-mary-button icon="o-bell" class="btn-circle btn-outline relative">
                    @if($notifications->count() > 0)
                        <span class="indicator-dot bg-green-500 w-3 h-3 rounded-full absolute top-0 left-0"></span>
                    @endif
                </x-mary-button>
            </div>
        </x-slot:trigger>

        <div id="notification-list" class="max-h-64 overflow-y-auto">
            @if($notifications->count() > 0)
                @foreach ($notifications as $notification)
                    <x-mary-menu-item
                        title="{{ $notification->data['message'] }}"
                        class="notification {{ $notification->read_at ? 'read' : 'unread' }} block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                    />
                @endforeach
            @else
                <x-mary-menu-item title="Vous n'avez pas encore de notifications" class="block px-4 py-2 text-sm leading-5 text-gray-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" />
            @endif
        </div>
    </x-mary-dropdown>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');

            Echo.private(`notification.${userId}`)
                .listen('NotificationEvent', (e) => {
                    console.log(e.notification);
                    addNotification(e.notification);
                });

            document.querySelector('.notification-indicator').addEventListener('click', function () {
                const notificationList = document.getElementById('notification-list');
                if (notificationList.classList.contains('open')) {
                    notificationList.classList.remove('open');
                    markAllAsRead();
                } else {
                    notificationList.classList.add('open');
                }
            });
        });

        function markAllAsRead() {
            axios.post('/notifications/mark-all-as-read')
                .then(response => {
                    document.querySelectorAll('.notification.unread').forEach(element => {
                        element.classList.remove('unread');
                        element.classList.add('read');
                    });

                    const indicatorDot = document.querySelector('.indicator-dot');
                    if (indicatorDot) {
                        indicatorDot.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error marking notifications as read:', error);
                });
        }

        function addNotification(notification) {
            const notificationList = document.getElementById('notification-list');
            const newNotification = document.createElement('div');
            newNotification.classList.add('notification', 'unread', 'block', 'px-4', 'py-2', 'text-sm', 'leading-5', 'text-gray-700', 'focus:outline-none', 'focus:bg-gray-100', 'transition', 'duration-150', 'ease-in-out');
            newNotification.textContent = notification.message;

            notificationList.insertBefore(newNotification, notificationList.firstChild);

            const indicatorDot = document.querySelector('.indicator-dot');
            if (indicatorDot) {
                indicatorDot.style.display = 'block';
            }
        }
    </script>
</div>
