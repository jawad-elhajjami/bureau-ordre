<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode, User $user)
    {
        $this->otpCode = $otpCode;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.otp-code')
                    ->subject('Votre code OTP')
                    ->with([
                        'otpCode' => $this->otpCode,
                        'user' => $this->user
                    ]);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Otp Code Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
