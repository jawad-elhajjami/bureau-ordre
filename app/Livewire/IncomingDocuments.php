<?php

namespace App\Livewire;

use App\Events\DocumentMarkedAsReadEvent;
use App\Models\Document;
use Illuminate\Support\Facades\Event;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Notifications\MarkedAsRead;

class IncomingDocuments extends Component
{
    use WithPagination;
    use Toast;

    public $search = '';
    public $incomingDocumentsCount;
    public array $sortBy = ['column' => 'order_number', 'direction' => 'asc'];

    #[On('search-changed')]
    public function searchChanged($value)
    {
        $this->search = $value;
    }

    #[On('update-documents')]
    public function refreshView(){
        $this->render();
    }

    public function sortBy($column)
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);

        if (!auth()->user()->can('delete-document', $document)) {
            $this->error("You are not authorized to delete this document.");
            return;
        }

        $document->delete();
        $this->success("Document ({$document->subject}) supprimé.");
        $this->dispatch('incomingDocumentDeleted');
    }

    public function toggleReadStatus($documentId)
    {
        $user = auth()->user();
        $document = Document::find($documentId);

        if ($document->readers->contains($user)) {
            $document->readers()->detach($user);
            $this->success(__('messages.mark_as_unread_success'));
        } else {
            $document->readers()->attach($user, ['read_at' => now()]);
            $this->success(__('messages.mark_as_read_success'));
            $this->notifyUsers($document);
        }
    }

    private function notifyUsers($document)
    {
        $owner = $document->owner;
        $reader = auth()->user();

        // inform document owner that someone read his document
        $owner->notify(new MarkedAsRead($document, $reader));

        // Dispatch the notification event once with the document's data
        Event::dispatch(new DocumentMarkedAsReadEvent($document, $reader));
    }

    public function render()
    {
        $headers = [
            ['key' => 'id', 'label' => 'Identifiant'],
            ['key' => 'order_number', 'label' => 'N ordre', 'sortable' => true],
            ['key' => 'subject', 'label' => 'Sujet', 'sortable' => true],
            ['key' => 'description', 'label' => 'Description', 'sortable' => true],
            ['key' => 'sent_by', 'label' => 'Envoyé par', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Envoyé le', 'sortable' => true]
        ];

        $documentsQuery = Document::query();
        $user = auth()->user();

        $documentsQuery->where(function ($query) use ($user) {
            // Include documents specifically sent to the user
            $query->where('recipient_id', $user->id);

            // Include documents sent to the user's service if recipient_id is null
            $query->orWhere(function ($query) use ($user) {
                $query->whereNull('recipient_id')
                      ->where('service_id', $user->service_id);
            });
        });


        if (!empty($this->search)) {
            $documentsQuery->where(function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('order_number', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $documents = $documentsQuery
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(25);

        $this->incomingDocumentsCount = $documents->count();
        $this->dispatch('count-changed', count: $this->incomingDocumentsCount);

        return view('livewire.incoming-documents', [
            'documents' => $documents,
            'headers' => $headers,
        ]);
    }
}
