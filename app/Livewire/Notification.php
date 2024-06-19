<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notification extends Component
{
    public $notifications;

    public function mount()
    {
        $this->fetchNotifications();
    }

    public function getListeners()
    {
        return [
            "echo-private:notification." . Auth::id() . ",NotificationEvent" => 'refreshNotifications',
        ];
    }

    public function refreshNotifications()
    {
        $this->fetchNotifications();
    }

    private function fetchNotifications()
    {
        $this->notifications = Auth::user()->unreadNotifications;
    }

    public function render()
    {
        return view('livewire.notification', ['notifications' => $this->notifications]);
    }
}
