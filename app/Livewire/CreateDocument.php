<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Service;
use App\Models\User;
use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpCodeMail;
use App\Notifications\DocumentCreated;
use Illuminate\Support\Facades\Event;
use App\Events\NotificationEvent;

class CreateDocument extends Component
{
    use WithFileUploads;
    use Toast;

    public $n_ordre;
    public $sujet;
    public $description;
    public $file;
    public $service;
    public $category;
    public $recipient;
    public $users = [];
    public $loggedInUserId;
    public $otp_code;
    public $otpcode = false;

    protected $rules = [
        'n_ordre' => 'required|min:3|max:50|string|unique:documents,order_number',
        'sujet' => 'required|min:3|max:255|string',
        'description' => 'nullable|min:3|max:255|string',
        'file' => 'required|mimes:pdf|max:2048',
        'service' => 'required|exists:services,id',
        'category' => 'required|exists:document_categories,id',
        'recipient' => 'nullable|exists:users,id'
    ];

    public function mount()
    {
        $this->loggedInUserId = auth()->user()->id;
        $this->n_ordre = $this->generateOrderNumber();
    }

    public function updatedService($value)
    {
        $this->users = User::where('service_id', $this->service)
            ->where('id', '!=', $this->loggedInUserId) // Exclude the logged-in user
            ->get();
        $this->recipient = null; // Reset recipient when service changes
    }

    public function save()
    {
        $this->validate();

        try {
            $filePath = $this->file->storeAs('', $this->n_ordre . '.' . $this->file->getClientOriginalExtension(), 'files');

            $otpCode = null;
            if ($this->otpcode) {
                $otpCode = $this->generateOtpCode();
                $sujet = $this->sujet;

                if ($this->recipient) {
                    // Send OTP to the selected recipient
                    $recipient = User::find($this->recipient);
                    Mail::to($recipient->email)->send(new OtpCodeMail($otpCode, $recipient, $sujet));
                } else {
                    // Send OTP to all users of the selected service if recipient is not selected
                    $serviceUsers = User::where('service_id', $this->service)
                        ->where('id', '!=', $this->loggedInUserId) // Exclude the logged-in user
                        ->get();

                    foreach ($serviceUsers as $user) {
                        Mail::to($user->email)->send(new OtpCodeMail($otpCode, $user, $sujet));
                    }
                }
            }

            $document = Document::create([
                'order_number' => $this->n_ordre,
                'subject' => $this->sujet,
                'file_path' => $filePath,
                'description' => $this->description,
                'category_id' => $this->category,
                'service_id' => $this->service,
                'recipient_id' => $this->recipient,
                'user_id' => auth()->user()->id,
                'otp_code' => $otpCode // Save OTP code if generated
            ]);

            // Notify the admin and recipient or service users
            $this->notifyUsers($document);

            $this->success('Document créé avec succès !');
            $this->resetExcept('n_ordre');

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function notifyUsers($document)
    {
        $admin = User::where('role_id', 1)->first();
        $recipient = $document->recipient;
        $creator = auth()->user();

        $notifiedUsers = [];

        // Notify the admin
        if ($admin->id !== $creator->id && !in_array($admin->id, $notifiedUsers)) {
            $admin->notify(new DocumentCreated($document, $creator));
            $notifiedUsers[] = $admin->id;
        }

        if ($recipient) {
            // Notify the recipient if specified
            if ($recipient->id !== $creator->id && !in_array($recipient->id, $notifiedUsers)) {
                $recipient->notify(new DocumentCreated($document, $creator));
                $notifiedUsers[] = $recipient->id;
            }
        } else {
            // Notify all users in the service except the creator
            $serviceUsers = User::where('service_id', $document->service_id)
                ->where('id', '!=', $creator->id)
                ->get();

            foreach ($serviceUsers as $user) {
                if (!in_array($user->id, $notifiedUsers)) {
                    $user->notify(new DocumentCreated($document, $creator));
                    $notifiedUsers[] = $user->id;
                }
            }
        }
        // Dispatch the notification event once with the document's data
        Event::dispatch(new NotificationEvent($document));
    }

    public function generateOtpCode()
    {
        return rand(100000, 999999);
    }

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

    public function render()
    {
        return view('livewire.create-document', [
            'categories' => DocumentCategory::all(),
            'services' => Service::all(),
            'users' => $this->users
        ]);
    }
}
