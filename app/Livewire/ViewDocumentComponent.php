<?php

namespace App\Livewire;

use App\Events\NoteNotificationEvent;
use App\Events\NotificationEvent;
use App\Models\Document;
use App\Models\Note;
use App\Models\User;
use App\Notifications\NoteCreated;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Smalot\PdfParser\Parser;

class ViewDocumentComponent extends Component
{
    use WithPagination;
    use Toast;

    public $document;
    public $selectedTab = 'details';
    public $metadata = [];
    public $content = '';

    #[Validate('required|min:3|max:1000|string')]
    public $note;

    public $columns = [
        'order_number' => 'N ordre',
        'category_id' => 'Category',
        'service_id' => 'Service conçu',
        'description' => 'Description',
        'subject' => 'Sujet',
        'recipient_id' => 'Employé conçu',
        'created_at' => 'Date de création',
        'updated_at' => 'Date de modification',
        // Add other columns as needed
    ];

    public function mount($id)
    {
        // Retrieve the document
        $this->document = Document::findOrFail($id);

        // Authorize the user to view the document
        if (Gate::denies('view-document', $this->document)) {
            abort(403, "You are not authorized to view this file.");
        }

        // Extract metadata
        $this->extractMetadata();
    }

    #[On('update-notes')]
    public function updateNotes(){
        $this->dispatch('refresh-view');
        $this->selectedTab = 'notes';
    }
    

    private function notifyUsers($note)
    {
        $creator = auth()->user();

        // Notify the creator of the document
        $documentOwner = $note->document->owner;
        if ($documentOwner && $documentOwner->id !== $creator->id) {
            $documentOwner->notify(new NoteCreated($note, $creator));
        }

        // notifiy document recipient(s)
        if($note->document->recipient_id != null){
            $recipient = User::findOrFail($note->document->recipient_id);
            $recipient->notify(new NoteCreated($note, $creator));
        }else{
            $serviceUsers = User::where('service_id', $note->document->service_id)
                ->where('id', '!=', $creator->id)
                ->get();

            foreach ($serviceUsers as $user) {
                $user->notify(new NoteCreated($note, $creator));
            }
        }

        // Dispatch the notification event once with the document's data
        Event::dispatch(new NoteNotificationEvent($note));
    }


    public function addNote()
    {
        $this->dispatch('refresh-view');
        $this->validate();

        try {
            $note = Note::create([
                'user_id' => auth()->user()->id,
                'document_id' => $this->document->id,
                'content' => $this->note
            ]);
            $this->success('Note ajouté avec succés !');
            $this->note = '';
            // Notify users about the note creation
            $this->notifyUsers($note);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function deleteNote($id)
    {
        $note = Note::findOrFail($id);
        if (Gate::denies('delete-note', $note)) {
            abort(403, "You are not authorized to delete this note.");
        }
        $note->delete();
        $this->error('Note ' . $note->id . ' supprimé');
        $this->dispatch('refresh-view');
    }

    public function getDocumentUrl()
    {
        // Generate a secure URL to the document
        return Storage::disk('files')->url($this->document->file_path);
    }

    private function extractMetadata()
    {
        $filePath = Storage::disk('files')->path($this->document->file_path);

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);

            // Get PDF metadata
            $pdfMetadata = $pdf->getDetails();
            $pdfText = $pdf->getText();

            // Get additional file information
            $fileInfo = [
                'original_file_name' => $this->document->file_path, // assuming the file_path stores the original name
                'file_size' => Storage::disk('files')->size($this->document->file_path),
                'mime_type' => Storage::disk('files')->mimeType($this->document->file_path),
                'md5_checksum' => md5_file($filePath),
            ];

            $this->metadata = array_merge($pdfMetadata ?: [], $fileInfo);
            $this->content = $pdfText ?: null;
        } catch (\Exception $e) {
            \Log::error('Error parsing PDF:', [$e->getMessage()]);
            $this->metadata = null;
        }
    }

    public function render()
    {
        $otherDocuments = Document::where('user_id', $this->document->user_id)
            ->where('id', '!=', $this->document->id) // Exclude the current document
            ->paginate(5);

        return view('livewire.view-document-component', [
            'notes' => Note::where('document_id', $this->document->id)
                ->orderBy('created_at', 'desc')
                ->get(),
            'notes_count' => Note::where('document_id', $this->document->id)->count(),
            'otherDocuments' => $otherDocuments,
        ]);
    }
}
