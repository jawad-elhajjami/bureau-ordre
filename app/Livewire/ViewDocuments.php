<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Component;

class ViewDocuments extends Component
{
    public $filtersDrawer = false;
    public $search = '';
    public $selectedTab = 'incoming';

    public function updatedSearch() {
        $this->dispatch('search-changed', $this->search);
    }

    public function render()
    {
        $user = auth()->user();
        return view('livewire.view-documents',[
            'incomingDocumentCount' => Document::where(function ($query) use ($user) {
                // Include documents specifically sent to the user
                $query->where('recipient_id', $user->id);
                
                // Include documents sent to the user's service if recipient_id is null
                $query->orWhere(function ($query) use ($user) {
                    $query->whereNull('recipient_id')
                          ->where('service_id', $user->service_id);
                });
            })->count()
        ]);
    }
}
