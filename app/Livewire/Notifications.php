<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
            timeout: 10000,                      // optional (ms)
        );
        $this->refreshNotifications();
    }

    public function playNotificationSound(){
            $this->dispatch('playSound');
    }

    #[On('notificationReceived')]
    public function showNotification($value){

        $documentTitle = $value['notification']['order_number'];
        $sender_id = $value['notification']['user_id'];
        $reciever = $value['notification']['recipient_id'];
        $recieving_service = $value['notification']['service_id'];
        $sender = User::where('id', $sender_id)->first()->name;
        $message = $sender . " vous a envoyé le document ". $documentTitle;

        // Exclude the sender from receiving the notification
        if (auth()->user()->id === $sender_id) {
            return;
        }

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

        $this->dispatch('update-documents');

    }

    #[On('noteNotificationRecieved')]
    public function showNoteNotification($notification)
    {
        $content = $notification['notification']['content'];
        $senderId = $notification['notification']['user_id'];
        $documentId = $notification['notification']['document_id'];

        $document = Document::findOrFail($documentId);

        $sender = User::findOrFail($senderId)->name;
        $documentSubject = $document->subject;
        $message = "{$sender} a ajouté une note sur le document {$documentSubject}: {$content}";
        
        // Exclude the sender from receiving the notification
        if (auth()->user()->id === $senderId) {
            return;
        }

        // show notitifcation to document owner
        if(auth()->user()->id == $document->owner->id){
            $this->display($message);
            $this->playNotificationSound();
        }

        // show notification toast to document recipients

        if($document->recipient_id != null || $document->recipient_id != ''){
            if(auth()->user()->id == $document->recipient_id){
                $this->display($message);
                $this->playNotificationSound();
            }
        }

        // show notification toast to document service members
        if($document->service_id != null && $document->recipient_id == null){
            if(auth()->user()->service_id == $document->service_id){
                $this->display($message);
                $this->playNotificationSound();
            }
        }

        // update notes list
        $this->dispatch('update-notes', tab: "notes" );

    }

    #[On('DocumentReadEvent')]
    public function showMarkedAsReadNotification($notification){
        $creator = auth()->user();
        $ownerId = $notification['notification']['user_id'];
        if($creator->id == $ownerId){
            $this->refreshNotifications();
            $this->playNotificationSound();
        }
    }

    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->refreshNotifications();
        $this->unreadCount = 0;
    }

    public function deleteAllNotifications(){
        Auth::user()->notifications()->delete();
        $this->refreshNotifications();
        $this->unreadCount = 0;
        $this->success('Tous les notifications ont été supprimées');
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}

