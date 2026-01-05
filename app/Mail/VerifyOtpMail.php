<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $studentName;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $studentName = null)
    {
        $this->otp = $otp;
        $this->studentName = $studentName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã OTP xác thực tài khoản sinh viên',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-otp',  // view email
            with: [
                'otp' => $this->otp,
                'studentName' => $this->studentName,
            ]
        );
    }

    /**
     * No attachments.
     */
    public function attachments(): array
    {
        return [];
    }
}
