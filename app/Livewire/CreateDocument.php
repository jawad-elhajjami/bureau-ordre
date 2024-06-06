<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Service;
use App\Models\User;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class CreateDocument extends Component
{

    use WithFileUploads;
    use Toast;
    
    // public CreateDocumentForm $form;

    #[Validate('required|min:3|max:50|string')] 
    public $n_ordre;

    #[Validate('required|min:3|max:255|string')] 
    public $sujet;

    #[Validate('nullable|min:3|max:255|string')] 
    public $description;

    #[Validate('required|file|max:2048')]
    public $file;

    #[Validate('required|exists:services,id')] 
    public $service;

    #[Validate('required|exists:document_categories,id')] 
    public $category;

    // Generate n_ordre
    public function generateNOrdre()
    {
        // Generate the n_ordre field value
        $yearMonth = date('Y_m');
        $latestId = Document::latest()->value('id') ?? 0;
        $this->n_ordre = $yearMonth . '_N' . str_pad($latestId + 1, 3, '0', STR_PAD_LEFT);
    }

    public function mount()
    {
        // Generate n_ordre when the component is mounted
        $this->generateNOrdre();
    }

    public function save(){
        // validate form
        $this->validate();

        // Store the file and get its path
        try{
            

            // Generate $n_ordre if not already set
            if (!$this->n_ordre) {
                $this->generateNOrdre();
            }

            $filePath = $this->file->storeAs('', $this->n_ordre . '.' . $this->file->getClientOriginalExtension(), 'files');

            
            // Create a new document
            Document::create([
                'order_number' => $this->n_ordre,
                'subject' => $this->sujet,
                'file_path' => $filePath ,
                'description' => $this->description,
                'category_id' => $this->category,
                'service_id' => $this->service,
                'user_id' => auth()->user()->id
            ]);
            $this->success('Document crée avec succès !');
            $this->resetExcept('n_ordre');
            

        }catch(Exception $e){
            $this->error($e->getMessage());
        }
        
        
    }

    public function render()
    {
        return view('livewire.create-document',[
                'categories' => DocumentCategory::all(),
                'services' => Service::all(),
                '$users' => User::all()
        ]);
    }
}
