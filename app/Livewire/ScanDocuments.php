<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Service;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class ScanDocuments extends Component
{

    use Toast;

    public $n_ordre;
    public $service;
    public $category;
    public $sujet;
    public $description;
    public $recipient;
    public $users = [];
    public $message = '';
    public $messageType = '';
    public $loggedInUserId;


    
    public static function generateOrderNumber()
    {
        $yearMonth = date('Y_m');
        $latestOrderNumber = Document::where('order_number', 'like', "{$yearMonth}%")
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        if ($latestOrderNumber) {
            $latestNumber = (int) substr($latestOrderNumber, strrpos($latestOrderNumber, '_N') + 2);
        } else {
            $latestNumber = 0;
        }

        return $yearMonth . '_N' . str_pad($latestNumber + 1, 3, '0', STR_PAD_LEFT);
    }
    
    public function mount() {
        $this->n_ordre = $this->generateOrderNumber();
        $this->loggedInUserId = auth()->user()->id;
    }

    public function updatedService($value)
    {
        $this->users = User::where('service_id', $this->service)
            ->where('id', '!=', $this->loggedInUserId) // Exclude the logged-in user
            ->get();
        $this->recipient = null; // Reset recipient when service changes
    }

    public function updatedMessage($value){
        if($this->messageType == 'success'){
            $this->success($value);
        }else{
            $this->error($value);
        }
    }

    public function render()
    {
        return view('livewire.scan-documents',[
            'categories' => DocumentCategory::all(),
            'services' => Service::all(),
            'users' => $this->users
        ]);
    }
}
