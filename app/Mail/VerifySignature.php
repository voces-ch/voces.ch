<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Signature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifySignature extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public Signature $signature;
    public ?Campaign $campaign;
    /**
     * Create a new message instance.
     */
    public function __construct(Signature $signature, ?Campaign $campaign = null)
    {
        $this->signature = $signature;
        $this->campaign = $campaign;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __("Thanks for signing! Please verify your email address"),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.verify-signature',
            with: [
                'verificationUrl' => url("/verify/signature/{$this->signature->uuid}/{$this->signature->verification_token}"),
                'campaignName' => $this->campaign ? $this->campaign->title : $this->signature->campaign->title,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
