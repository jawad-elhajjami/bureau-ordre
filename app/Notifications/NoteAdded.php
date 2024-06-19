<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Note;

class NoteAdded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $note;
    public $creator;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Note $note, $creator)
    {
        $this->note = $note;
        $this->creator = $creator;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'A new note has been added by ' . $this->creator->name . '.',
            'created_by' => $this->creator->name,
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new note has been added to your document.')
                    ->action('View Note', route('view-document', $this->note->document_id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'note_id' => $this->note->id,
            'document_id' => $this->note->document_id,
            'message' => 'A new note has been added to your document.',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'note_id' => $this->note->id,
            'document_id' => $this->note->document_id,
            'message' => 'A new note has been added to your document.',
        ]);
    }
}
