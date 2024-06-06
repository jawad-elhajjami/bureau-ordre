<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ViewDocumentComponent extends Component
{
    public $document;

    public function mount($id)
    {
        // Retrieve the document
        $this->document = Document::findOrFail($id);

        // Authorize the user to view the document
        if (Gate::denies('view-document', $this->document)) {
            abort(403, "You are not authorized to view this file.");
        }
    }

    public function render()
    {
        return view('livewire.view-document-component');
    }

    public function getDocumentUrl()
    {
        // Generate a secure URL to the document
        return Storage::disk('files')->url($this->document->file_path);
    }
}
