<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Service;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageDocuments extends Component
{
    use WithPagination;
    use Toast;
    public bool $filtersDrawer = false;
    public array $sortBy = ['column' => 'order_number', 'direction' => 'asc'];
    public $search = '';
    public $filterCategory = null;
    public $filterService = null;
    public $filterRecipient = null;
    public $timePeriodFilter = null;
    public $timePeriodOptions = [
        ['value' => 'last_7_days', 'label' => 'Les 7 derniers jours'],
        ['value' => 'last_30_days', 'label' => 'Les 30 derniers jours'],
        ['value' => 'last_90_days', 'label' => 'Les 90 derniers jours'],
        ['value' => 'this_month', 'label' => 'Ce mois-ci'],
        ['value' => 'last_month', 'label' => 'Le mois dernier'],
        ['value' => 'this_year', 'label' => 'Cette année'],
        ['value' => 'last_year', 'label' => 'L\'année dernière'],
    ];

    public function sortBy($column)
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->filterCategory = null;
        $this->filterService = null;
        $this->filterRecipient = null;
        $this->timePeriodFilter = null;
        $this->success('Filtres réinitialisés');
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
        }
    }

    #[On('update-documents')]
    public function refreshView(){
        $this->render();
    }


    public function render()
    {
        $headers = [
            // ['key' => 'id', 'label' => 'Identifiant'],
            ['key' => 'order_number', 'label' => 'N ordre', 'sortable' => true],
            ['key' => 'subject', 'label' => 'Sujet', 'sortable' => true],
            ['key' => 'description', 'label' => 'Description', 'sortable' => true],
            ['key' => 'sent_by', 'label' => 'Envoyé par', 'sortable' => false],
            ['key' => 'department_sent_to', 'label' => 'Service conçu', 'sortable' => false],
            ['key' => 'user_sent_to', 'label' => 'Employé conçu', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Envoyé le', 'sortable' => true],
            // ['key' => 'updated_at', 'label' => 'Modifié le', 'sortable' => true],
        ];

        $documentsQuery = Document::query();

        if (!empty($this->search)) {
            $documentsQuery->where(function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('order_number', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterCategory) {
            $documentsQuery->where('category_id', $this->filterCategory);
        }

        if ($this->filterService) {
            $documentsQuery->where('service_id', $this->filterService);
        }

        if ($this->filterRecipient) {
            $documentsQuery->where('recipient_id', $this->filterRecipient);
        }

        if ($this->timePeriodFilter) {
            switch ($this->timePeriodFilter) {
                case 'last_7_days':
                    $documentsQuery->whereDate('created_at', '>=', now()->subDays(7));
                    break;
                case 'last_30_days':
                    $documentsQuery->whereDate('created_at', '>=', now()->subDays(30));
                    break;
                case 'last_90_days':
                    $documentsQuery->whereDate('created_at', '>=', now()->subDays(90));
                    break;
                case 'this_month':
                    $documentsQuery->whereYear('created_at', now()->year)
                                   ->whereMonth('created_at', now()->month);
                    break;
                case 'last_month':
                    $documentsQuery->whereYear('created_at', now()->subMonth()->year)
                                   ->whereMonth('created_at', now()->subMonth()->month);
                    break;
                case 'this_year':
                    $documentsQuery->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $documentsQuery->whereYear('created_at', now()->subYear()->year);
                    break;
            }
        }

        $documents = $documentsQuery
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(25);

        // Check if any filters are applied
        if ($this->filterCategory || $this->filterService || $this->filterRecipient || $this->timePeriodFilter) {
            $this->success('Filtre appliquée');
        }

        return view('livewire.manage-documents', [
            'documents' => $documents,
            'headers' => $headers,
            'users' => User::all(),
            'categories' => DocumentCategory::all(),
            'services' => Service::all()
        ]);
    }
}
