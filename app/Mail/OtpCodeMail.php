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
    public $mailSubject;

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode, User $user, $mailSubject)
    {
        $this->otpCode = $otpCode;
        $this->user = $user;
        $this->mailSubject = $mailSubject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code OTP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.otp-code',
            with: [
                'otpCode' => $this->otpCode,
                'user' => $this->user,
                'subject' => $this->subject,
            ],
        );
    }

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
