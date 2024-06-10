<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\note;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Smalot\PdfParser\Parser;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ViewDocumentComponent extends Component
{

    use WithPagination;
    use Toast;

    public $document;

    // Add the $persist property to maintain Livewire state across navigation actions
    protected $persist = true;
    
    public $selectedTab = 'details';
    public $metadata = [];
    public $otherDocuments = [];
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

        // Find other documents by the same user
        $this->otherDocuments = Document::where('user_id', $this->document->user_id)
        ->where('id', '!=', $id) // Exclude the current document
        ->get();

    }

    public function addNote(){
        $this->dispatch('refresh-view');
        $this->validate();

        try{
            Note::create([
                'user_id' => auth()->user()->id,
                'document_id' => $this->document->id,
                'content' => $this->note
            ]);
            $this->success('Note ajouté avec succés !');
            $this->note = '';
        }catch(Exception $e){
            $this->error($e->getMessage());
        }

    }

    public function deleteNote($id){
        $note = Note::findOrFail($id);
        if (Gate::denies('delete-note', $note)) {
            abort(403, "You are not authorized to delete this note.");
        }
        $note->delete();
        $this->error('Note '. $note->id . ' supprimé');
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
        return view('livewire.view-document-component',[
            'notes' => Note::where('document_id', $this->document->id)->get(),
            'notes_count' =>  note::where('document_id', $this->document->id)->count()
        ]);
    }
    

}
