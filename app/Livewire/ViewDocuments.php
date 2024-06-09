<?php

namespace App\Livewire;

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
        return view('livewire.view-documents');
    }
}
