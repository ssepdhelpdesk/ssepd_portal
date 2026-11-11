<?php

namespace App\Mail;

use App\Models\DisabilityDayRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrackingOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public DisabilityDayRegistration $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, DisabilityDayRegistration $registration)
    {
        $this->otp = $otp;
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification Code (OTP) for Application Tracking – SSEPD Odisha',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.disability_day.tracking_otp',
            with: [
                'otp'          => $this->otp,
                'registration' => $this->registration,
                'validMinutes' => 5,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

