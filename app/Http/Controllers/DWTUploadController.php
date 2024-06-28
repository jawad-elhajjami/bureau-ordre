<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Mail\OtpCodeMail;
use App\Notifications\DocumentCreated;
use Exception;
use Illuminate\Support\Facades\Event;

class DWTUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the uploaded file and other fields
        $validation = Validator::make($request->all(), [
            'RemoteFile' => 'required|mimes:pdf|max:2048', // Validate that the file is a PDF and not larger than 2MB
            'sujet' => 'required|string|max:255',
            'service' => 'required|integer',
            'category' => 'required|integer',
            'n_ordre' => 'required|string|max:255',
            'recipient' => 'nullable|integer|exists:users,id', // Make recipient optional
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()->all()
            ], 422); // Use 422 for validation errors
        }

        // Process the uploaded file
        if ($request->hasFile('RemoteFile')) {
            try {
                $file = $request->file('RemoteFile');
                $filename = $file->getClientOriginalName();

                // Store the file on the 'files' disk
                Storage::disk('files')->put($filename, file_get_contents($file->getRealPath()));

                // Generate OTP if the checkbox is checked
                $otpCode = null;
                if ($request->has('otpcode') && $request->otpcode == 1) {
                    $otpCode = $this->generateOtpCode();
                    $sujet = $request->sujet;

                    // Send OTP email
                    if ($request->recipient) {
                        // Send OTP to the selected recipient
                        $recipient = User::find($request->recipient);
                        Mail::to($recipient->email)->send(new OtpCodeMail($otpCode, $recipient, $sujet));
                    } else {
                        // Send OTP to all users of the selected service if recipient is not selected
                        $serviceUsers = User::where('service_id', $request->service)
                            ->where('id', '!=', auth()->user()->id)
                            ->get();

                        foreach ($serviceUsers as $user) {
                            Mail::to($user->email)->send(new OtpCodeMail($otpCode, $user, $sujet));
                        }
                    }
                }

                // Create the document record
                $document = Document::create([
                    'user_id' => auth()->user()->id,
                    'file_path' => $filename,
                    'subject' => $request->sujet,
                    'service_id' => $request->service,
                    'category_id' => $request->category,
                    'recipient_id' => $request->recipient,
                    'description' => $request->description,
                    'order_number' => $request->n_ordre,
                    'otp_code' => $otpCode, // Only save OTP code if it was generated
                ]);

                $this->notifyUsers($document);

                return response()->json([
                    'type' => 'success',
                    'message' => 'Successfully uploaded.'
                ], 200); // Use 200 for success
            } catch (Exception $e) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'File upload failed. ' . $e->getMessage()
                ], 500); // Use 500 for other errors
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'File upload failed. No file received.'
            ], 500); // Use 500 for other errors
        }
    }

    private function generateOtpCode()
    {
        return rand(100000, 999999);
    }

    private function sendOTPMail($recipientId, $serviceId, $otp)
    {
        $sujet = request()->sujet;

        // If recipient ID is provided, send to the specific recipient
        if ($recipientId) {
            $recipient = User::find($recipientId);
            if ($recipient) {
                Mail::to($recipient->email)->send(new OtpCodeMail($otp, $recipient, $sujet));
            }
        } else {
            // If no recipient ID, send to all members of the service
            $serviceUsers = User::where('service_id', $serviceId)
                ->where('id', '!=', auth()->user()->id)
                ->get();
            foreach ($serviceUsers as $user) {
                Mail::to($user->email)->send(new OtpCodeMail($otp, $user, $sujet));
            }
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
}
