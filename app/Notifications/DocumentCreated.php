<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            'message' => 'Un nouveau document a été créé par ' . $this->creator->name . '.',
            'document_id' => $this->document->id,
            'document_title' => $this->document->subject,
            'created_by' => $this->creator->name,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Un nouveau document a été créé par ' . $this->creator->name . '.',
            'document_id' => $this->document->id,
            'document_title' => $this->document->subject,
            'created_by' => $this->creator->name,
        ];
    }
}

