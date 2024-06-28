<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MarkedAsRead extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;
    public $reader;

    public function __construct(Document $document, User $reader)
    {
        $this->document = $document;
        $this->reader = $reader;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'document' => $this->document->order_number,
            'document_id' => $this->document->id,
            'marked_by' => $this->reader->name,
            'reader_id' => $this->reader->id,
            'message' => 'Votre document (' . $this->document->order_number . ') a été lu par '. $this->reader->name,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'document' => $this->document->order_number,
            'document_id' => $this->document->id,
            'marked_by' => $this->reader->name,
            'reader_id' => $this->reader->id,
            'message' => 'Votre document (' . $this->document->order_number . ') a été lu par '. $this->reader->name,
        ]);
    }
}

