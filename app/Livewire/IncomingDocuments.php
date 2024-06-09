<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class IncomingDocuments extends Component
{
    use WithPagination;
    use Toast;

    public $search;
    public $incomingDocumentsCount;
    public array $sortBy = ['column' => 'order_number', 'direction' => 'asc'];


    public function mount($search)
    {
        $this->search = $search;
    }

    #[On('search-changed')]
    public function searchChanged($value)
    {
        // Access the value sent with the event
        $this->search = $value;
        // Perform any other actions based on the search value
        // For example, you can call other methods or update other properties
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
        
        // Check if the user has permission to delete the document
        if (!auth()->user()->can('delete-document', $document)) {
            // Unauthorized, display error message
            $this->error("You are not authorized to delete this document.");
            return;
        }

        // Delete the document
        $document->delete();
        
        // Display success message
        $this->success("Document ({$document->subject}) supprimé.");

        $this->dispatch('incomingDocumentDeleted');

    }

    public function render()
    {
        $headers = [
            ['key' => 'id', 'label' => 'Identifiant'],
            ['key' => 'order_number', 'label' => 'N ordre', 'sortable' => true],
            ['key' => 'subject', 'label' => 'Sujet', 'sortable' => true],
            ['key' => 'description', 'label' => 'Description', 'sortable' => true],
            ['key' => 'sent_by', 'label' => 'Envoyé par', 'sortable' => true],
        ];

        // Start building the query
        $documentsQuery = Document::query();
        $user = auth()->user();
        
        // Filter for incoming documents
        $documentsQuery->where('service_id', $user->service_id)->orWhere('recipient_id', auth()->user()->id);

        if (!empty($this->search)) {
            $documentsQuery->where(function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('order_number', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        

        // Sorting
        $documents = $documentsQuery
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(4);

        $this->incomingDocumentsCount = $documents->count();
        $this->dispatch('count-changed', count: $this->incomingDocumentsCount);
        
        return view('livewire.incoming-documents', [
            'documents' => $documents,
            'headers' => $headers,
        ]);
    }
}
