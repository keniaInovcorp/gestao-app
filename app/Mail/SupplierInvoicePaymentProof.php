<?php

namespace App\Mail;

use App\Models\SupplierInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SupplierInvoicePaymentProof extends Mailable
{
    use Queueable, SerializesModels;

    public SupplierInvoice $supplierInvoice;

    /**
     * Create a new message instance.
     */
    public function __construct(SupplierInvoice $supplierInvoice)
    {
        $this->supplierInvoice = $supplierInvoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprovativo de Pagamento - Fatura ' . $this->supplierInvoice->number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier-invoice-payment-proof',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->supplierInvoice->payment_proof && Storage::disk('private')->exists($this->supplierInvoice->payment_proof)) {
            $attachments[] = Attachment::fromStorageDisk('private', $this->supplierInvoice->payment_proof)
                ->as('comprovativo-pagamento-' . $this->supplierInvoice->number . '.' . pathinfo($this->supplierInvoice->payment_proof, PATHINFO_EXTENSION));
        }

        return $attachments;
    }
}
