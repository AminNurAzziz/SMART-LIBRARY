<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data_email;
    public $qrCodePath;

    /**
     * Create a new message instance.
     */
    public function __construct($data_email, $qrCodePath)
    {
        $this->data_email = $data_email;
        $this->qrCodePath = $qrCodePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        Log::info('Sending email to ' . $this->data_email['receiver_email']);
        return new Envelope(
            from: $this->data_email['sender_email'],
            to: $this->data_email['receiver_email'],
            subject: $this->data_email['subject']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'kirim_email'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->qrCodePath)
        ];
    }
}
