<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     */
    public function __construct(public string $name, public string  $email, public string  $body, public string  $phone)
    {

    }

    /**
     * Get the message content definition.
     */
    // public function build()
    // {
    //     return $this->subject('Mail from ATS')->replyTo($this->email)->view('mail.contactMail');
    // }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mail from ATS',
        );
    }

    public function content(): Content
    {
        return new Content('mail.contactMail');
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
