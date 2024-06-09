<?php

namespace App\Livewire;

use Livewire\Component;

class ViewDocuments extends Component
{
    public $filtersDrawer = false;
    public $search = '';
    public array $sortBy = ['column' => 'order_number', 'direction' => 'asc'];


    public function sortBy($column)
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }
    public $selectedTab = 'incoming';

    public function updatedSearch() {
        $this->dispatch('search-changed', $this->search);
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
            ->paginate(10);

        return view('livewire.view-documents', [
            'documents' => $documents,
            'headers' => $headers
        ]);
        return view('livewire.view-documents');
    }
}
