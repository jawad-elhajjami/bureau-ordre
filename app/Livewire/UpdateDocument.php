<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile as SupportFileUploadsTemporaryUploadedFile;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpCodeMail;

class UpdateDocument extends Component
{
    use WithFileUploads;
    use Toast;

    public $document;

    public $n_ordre;
    public $sujet;
    public $description;
    public $file;
    public $service;
    public $category;
    public $recipient;
    public $loggedInUserId;
    public $users = [];
    public $otpcode;

    public function mount($id)
    {
        $this->loggedInUserId = auth()->user()->id;

        // Retrieve the document
        $this->document = Document::findOrFail($id);

        // Authorize the user to view the document
        if (Gate::denies('update-document', $this->document)) {
            abort(403, "You are not authorized to update this document.");
        }

        $this->n_ordre = $this->document->order_number;
        $this->sujet = $this->document->subject;
        $this->description = $this->document->description;
        $this->file = null; // Initialize as null, as we don't want to load the existing file into the input
        $this->category = $this->document->category_id;
        $this->recipient = $this->document->recipient_id;
        $this->service = $this->document->service_id;
        $this->otpcode = !empty($this->document->otp_code); // Set checkbox state based on the presence of OTP code

        $this->users = User::where('service_id', $this->service)
            ->where('id', '!=', $this->loggedInUserId) // Exclude the logged-in user
            ->get();
    }

    public function updatedService($value)
    {
        $this->users = User::where('service_id', $this->service)
            ->where('id', '!=', $this->loggedInUserId) // Exclude the logged-in user
            ->get();
        $this->recipient = null; // Reset recipient when service changes
    }

    protected function rules()
    {
        return [
            'n_ordre' => 'required|min:3|max:50|string',
            'sujet' => 'required|min:3|max:255|string',
            'description' => 'nullable|min:3|max:255|string',
            'file' => 'nullable|file|max:2048|required_if:file,TemporaryUploadedFile',
            'service' => 'required|exists:services,id',
            'category' => 'required|exists:document_categories,id',
            'recipient' => 'nullable|exists:users,id'
        ];
    }

    public function save()
    {
        // Validate form
        $this->validate();
        if (empty($this->recipient)) {
            $this->recipient = null;
        }
        try {
            // Check if a new file is uploaded
            if ($this->file instanceof SupportFileUploadsTemporaryUploadedFile) {
                // Delete the old file if a new one is uploaded
                Storage::disk('files')->delete($this->document->file_path);
                // Store the new file
                $filePath = $this->file->storeAs('', $this->n_ordre . '.' . $this->file->getClientOriginalExtension(), 'files');
            } else {
                // Keep the old file path if no new file is uploaded
                $filePath = $this->document->file_path;
            }

            // Handle OTP code logic
            if ($this->otpcode) {
                // Generate OTP code if checkbox is checked
                $otpCode = $this->generateOtpCode();
                // Send OTP code to the recipient
                $this->sendOtpCode($otpCode);
            } else {
                // Remove OTP code if checkbox is unchecked
                $otpCode = null;
            }

            // Update the document
            $this->document->update([
                'order_number' => $this->n_ordre,
                'subject' => $this->sujet,
                'file_path' => $filePath,
                'description' => $this->description,
                'category_id' => $this->category,
                'service_id' => $this->service,
                'recipient_id' => $this->recipient,
                'user_id' => auth()->user()->id,
                'otp_code' => $otpCode,
                'requires_otp' => $this->otpcode
            ]);

            $this->success('Document mis à jour avec succès !');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function generateOtpCode()
    {
        return rand(100000, 999999);
    }

    public function sendOtpCode($otpCode)
    {
        if ($this->recipient) {
            // Send OTP to the selected recipient
            $recipient = User::find($this->recipient);
            Mail::to($recipient->email)->send(new OtpCodeMail($otpCode, $recipient));
        } else {
            // Send OTP to all users of the selected service if recipient is not selected
            $serviceUsers = User::where('service_id', $this->service)
                ->where('id', '!=', $this->loggedInUserId)
                ->get();

            foreach ($serviceUsers as $user) {
                Mail::to($user->email)->send(new OtpCodeMail($otpCode, $user));
            }
        }
    }

    public function render()
    {
        return view('livewire.update-document', [
            'categories' => DocumentCategory::all(),
            'services' => Service::all(),
            'users' => $this->users
        ]);
    }
}
