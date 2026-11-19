<?php

namespace App\Mail;

use App\Models\DisabilityDayRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StallAllotmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public DisabilityDayRegistration $registration;
    public array $allottedStalls;

    /**
     * Create a new message instance.
     *
     * @param DisabilityDayRegistration $registration
     * @param array $allottedStalls
     */
    public function __construct(DisabilityDayRegistration $registration, array $allottedStalls = [])
    {
        $this->registration = $registration;
        $this->allottedStalls = !empty($allottedStalls) ? $allottedStalls : $registration->getAllottedStallNumbers();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Disability Day 2026 – Stall Allotment Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.disability_day.stall_allotment_confirmation',
            with: [
                'registration'   => $this->registration,
                'allottedStalls' => $this->allottedStalls,
                'eventName'      => 'International Day of Persons With Disabilities – 2026',
                'venue'          => "S'O'A University",
                'eventDates'     => '01 December 2026 to 03 December 2026',
                'trackingUrl'    => url('/disability-day-2026/track'),
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
