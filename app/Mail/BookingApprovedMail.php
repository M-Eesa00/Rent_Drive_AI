<?php

namespace App\Mail;

use App\Models\Bookings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Bookings $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Rent Drive booking is approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.approved',
        );
    }
}
