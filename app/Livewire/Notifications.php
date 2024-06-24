<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Mary\Traits\Toast;

class Notifications extends Component
{
    use Toast;

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

    public function display($message){
        $this->toast(
            type: 'success',
            title: $message,
            description: 'New notification',                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-info',                  // Optional (daisyUI classes)
            timeout: 5000,                      // optional (ms)
        );
        $this->refreshNotifications();
    }

    public function playNotificationSound($notification){
        if($notification['recipient_id'] == auth()->user()->id || $notification['service_id'] == auth()->user()->service_id){
            $this->dispatch('playSound');
        }
    }

    #[On('notificationReceived')]
    public function showNotification($value){

        $documentTitle = $value['notification']['order_number'];
        $sender_id = $value['notification']['user_id'];
        $reciever = $value['notification']['recipient_id'];
        $recieving_service = $value['notification']['service_id'];
        $sender = User::where('id', $sender_id)->first()->name;
        $message = $sender . " vous a envoyé le document ". $documentTitle;

        if($reciever != null || $reciever != ''){
            if($reciever == auth()->user()->id){
                $this->display($message);
                $this->playNotificationSound($value['notification']);
;            }
        }else{
            if($recieving_service != null || $recieving_service != ''){
                if($recieving_service == auth()->user()->service_id){
                    $this->display($message);
                    $this->playNotificationSound($value['notification']);
                }
            }
        }

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

