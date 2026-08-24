<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Invoice;

class InvoiceList extends Component
{
    use WithPagination;

    public $status = '';
    public $search = '';

    public function updating($property)
    {
        if (in_array($property, ['status', 'search'])) {
            $this->resetPage();
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Invoice::with(['client.company', 'project'])->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'ilike', '%' . $this->search . '%')
                  ->orWhereHas('client.company', function ($q2) {
                      $q2->where('name', 'ilike', '%' . $this->search . '%');
                  });
            });
        }

        return view('livewire.invoices.invoice-list', [
            'invoices' => $query->paginate(15)
        ]);
    }
}
