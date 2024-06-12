<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Service;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $documentStats;
    public $documentsByCategory;
    public $documentsPerMonth;

    public function mount()
    {
        $this->documentStats = [
            'documents' => Document::count(),
            'categories' => DocumentCategory::count(),
            'users' => User::count(),
            'services' => Service::count(),
        ];

        $this->documentsByCategory = $this->getTopCategoriesWithDocuments();
        $this->documentsPerMonth = $this->getDocumentsPerMonth();
    }

    public function getTopCategoriesWithDocuments(): Collection
    {
        return DocumentCategory::withCount('documents')
            ->orderBy('documents_count', 'desc')
            ->take(7)
            ->get();
    }

    public function getDocumentsPerMonth()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subMonths(11)->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        // Initialize an array with the last 12 months
        $documentsPerMonth = collect();
        for ($i = 0; $i < 12; $i++) {
            $month = $now->copy()->subMonths($i)->format('Y-m');
            $documentsPerMonth->put($month, 0);
        }

        // Get the actual document count per month
        $documentCounts = Document::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Merge the counts into the initialized array
        return $documentsPerMonth->merge($documentCounts);
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'documentStats' => $this->documentStats,
            'documentsByCategory' => $this->documentsByCategory,
            'documentsPerMonth' => $this->documentsPerMonth,
        ]);
    }
}
