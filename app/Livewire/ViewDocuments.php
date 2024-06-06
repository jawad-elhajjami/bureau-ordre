<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ViewDocuments extends Component
{

    use WithPagination;
    use Toast;

    public bool $filtersDrawer = false;
    public $search = '';


    public function render()
    {
        $headers = [
            ['key' => 'id', 'label' => 'Identifiant'],
            ['key' => 'order_number', 'label' => 'N ordre'],
            ['key' => 'subject', 'label' => 'Sujet'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'sent_by', 'label' => 'Envoyé par'],

        ];
        return view('livewire.view-documents',[
            'documents' => Document::where('subject', 'LIKE', '%' . $this->search . '%')->paginate(10),
            'headers' => $headers
        ]);
    }
}
