<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Component;

class ViewDocuments extends Component
{

    public bool $filtersDrawer = false;

    public function render()
    {
        return view('livewire.view-documents',[
            'documents' => Document::all()
        ]);
    }
}
