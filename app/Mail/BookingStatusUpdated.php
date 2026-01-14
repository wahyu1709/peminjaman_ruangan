<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status;
    public $comment;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, string $status, ?string $comment = null)
    {
        $this->booking = $booking;
        $this->status = $status;
        $this->comment = $comment;
    }

    public function build(){
        if ($this->status === 'approved'){
            return $this->subject('Peminjaman Ruangan Disetujui')
                        ->markdown('emails.booking.approved');
        } else {
            return $this->subject('Peminjaman Ruangan Ditolak')
                        ->markdown('emails.booking.rejected');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Status Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
