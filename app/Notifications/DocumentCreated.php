<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class DocumentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;
    public $creator;

    public function __construct($document, $creator)
    {
        $this->document = $document;
        $this->creator = $creator;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'document_name' => $this->document->subject,
            'document_creator' => $this->creator->name,
            'message' => "Un nouveau document " . $this->document->subject . " créé par " . $this->creator->name,
            'link' => url('/documents/' . $this->document->id),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'document_name' => $this->document->subject,
            'document_creator' => $this->creator->name,
            'message' => "Un nouveau document " . $this->document->subject . " créé par " . $this->creator->name,
            'link' => url('/documents/' . $this->document->id),
        ]);
    }
}

