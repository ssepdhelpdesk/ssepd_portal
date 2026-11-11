<?php

namespace App\Mail;

use App\Models\DisabilityDayRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StallRegistrationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public DisabilityDayRegistration $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(DisabilityDayRegistration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Stall Registration Confirmation – International Day of Persons With Disabilities 2026',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.disability_day.stall_registration_confirmation',
            with: [
                'registration' => $this->registration,
                'eventName'    => 'International Day of Persons With Disabilities – 2026',
                'venue'        => "S'O'A University",
                'eventDates'   => '01 December 2026 to 03 December 2026',
                'trackingUrl'  => url('/disability-day-2026/track'),
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

