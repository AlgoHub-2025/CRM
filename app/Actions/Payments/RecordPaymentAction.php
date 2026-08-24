<?php
namespace App\Actions\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\DB;

class RecordPaymentAction
{
    public function execute(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            // Lock the invoice row to prevent concurrent payment race conditions
            $lockedInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

            $oldValues = $lockedInvoice->only(['paid_amount', 'status']);

            $payment = Payment::create([
                'invoice_id' => $lockedInvoice->id,
                'client_id' => $lockedInvoice->client_id,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
                'received_by' => $data['received_by'] ?? auth()->user()->employee->id,
                'notes' => $data['notes'] ?? null,
            ]);

            $newPaidAmount = $lockedInvoice->paid_amount + $payment->amount;
            
            $newStatus = $lockedInvoice->status;
            if ($newPaidAmount >= $lockedInvoice->total) {
                $newStatus = 'paid';
            } elseif ($newPaidAmount > 0 && $newPaidAmount < $lockedInvoice->total) {
                $newStatus = 'partially_paid';
            }

            $lockedInvoice->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
            ]);

            AuditableAction::dispatch(
                $lockedInvoice,
                'payment_recorded',
                'Invoices',
                $oldValues,
                $lockedInvoice->only(['paid_amount', 'status'])
            );

            return $payment;
        });
    }
}
