<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $notifications;
    public $unreadCount;

    protected $listeners = ['notificationReceived' => 'refreshNotifications'];

    public function mount()
    {
        $this->notifications = Auth::user()->notifications;
        $this->unreadCount = Auth::user()->unreadNotifications->count();
    }

    public function refreshNotifications()
    {
        $this->mount();
    }

    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}

