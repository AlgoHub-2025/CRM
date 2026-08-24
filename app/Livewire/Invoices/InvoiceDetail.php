<?php
namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Actions\Payments\RecordPaymentAction;
use Illuminate\Support\Facades\Auth;

class InvoiceDetail extends Component
{
    public Invoice $invoice;

    public $paymentAmount = 0;
    public $paymentMethod = 'bank_transfer';
    public $paymentReference = '';
    public $showPaymentModal = false;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function recordPayment(RecordPaymentAction $action)
    {
        $this->authorize('create', \App\Models\Payment::class);

        $action->execute($this->invoice, [
            'amount' => $this->paymentAmount,
            'method' => $this->paymentMethod,
            'transaction_reference' => $this->paymentReference,
        ]);

        $this->showPaymentModal = false;
        $this->paymentAmount = 0;
        $this->paymentReference = '';
        $this->invoice->refresh();
    }

    public function render()
    {
        return view('livewire.invoices.invoice-detail');
    }
}
