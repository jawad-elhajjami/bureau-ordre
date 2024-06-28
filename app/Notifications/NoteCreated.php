<?php

namespace App\Notifications;

use App\Models\Note;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NoteCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $note;
    public $creator;

    public function __construct(Note $note, User $creator)
    {
        $this->note = $note;
        $this->creator = $creator;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'note_id' => $this->note->id,
            'note_content' => $this->note->content,
            'message' => $this->creator->name . ' a ajouté une nouvelle note : ' . Str::limit($this->note->content, 15, $end = '...') . ' dans le document '. $this->note->document->subject,
            'link' => null,
            // 'link' => url('/notes/' . $this->note->id),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'note_id' => $this->note->id,
            'note_content' => $this->note->content,
            'message' => $this->creator->name . 'a ajouté une nouvelle note : ' . Str::limit($this->note->content, 15, $end = '...') . ' dans le document '. $this->note->document->subject,
            'link' => null,
            // 'link' => url('/notes/' . $this->note->id),
        ]);
    }
}
