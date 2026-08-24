<?php
namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceBuilder extends Component
{
    public ?Invoice $invoice = null;
    public $clientId;
    public $issueDate;
    public $dueDate;
    public $items = [];

    // Store tax/discount as percentages (e.g. 15 for 15%)
    public $taxPercent = 0;
    public $discountPercent = 0;

    public function mount(?Invoice $invoice = null, $clientId = null)
    {
        $this->invoice = $invoice;
        if ($this->invoice && $this->invoice->exists) {
            $this->clientId = $this->invoice->client_id;
            $this->issueDate = $this->invoice->issue_date ? $this->invoice->issue_date->format('Y-m-d') : null;
            $this->dueDate = $this->invoice->due_date ? $this->invoice->due_date->format('Y-m-d') : null;
            
            // Derive percentages from DB amounts
            // DB amounts are stored as raw money. Subtotal is the sum of items.
            $subtotal = $this->invoice->items->sum('line_total');
            if ($subtotal > 0) {
                $this->taxPercent = round(($this->invoice->tax / $subtotal) * 100, 2);
                $this->discountPercent = round(($this->invoice->discount / $subtotal) * 100, 2);
            }
            
            $this->items = $this->invoice->items->toArray();
        } else {
            $this->clientId = $clientId;
            $this->issueDate = now()->format('Y-m-d');
            $this->dueDate = now()->addDays(30)->format('Y-m-d');
        }
    }

    public function updateOrder($orderedIds)
    {
        if (!$this->invoice || !$this->invoice->exists) {
            return;
        }

        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                InvoiceItem::where('id', $id)
                    ->where('invoice_id', $this->invoice->id)
                    ->update(['sort_order' => $index]);
            }
        });
        
        $this->items = $this->invoice->items()->orderBy('sort_order')->get()->toArray();
    }

    public function save()
    {
        // Authorize either update (if editing) or create
        if ($this->invoice && $this->invoice->exists) {
            $this->authorize('update', $this->invoice);
        } else {
            $this->authorize('create', Invoice::class);
        }

        $this->validate([
            'clientId' => 'required|exists:clients,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01|max:9999999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999999',
            'taxPercent' => 'nullable|numeric|min:0|max:100',
            'discountPercent' => 'nullable|numeric|min:0|max:100',
        ]);

        $subtotal = collect($this->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $taxRaw = ($this->taxPercent / 100) * $subtotal;
        $discountRaw = ($this->discountPercent / 100) * $subtotal;
        $totalRaw = $subtotal + $taxRaw - $discountRaw;

        DB::transaction(function () use ($subtotal, $taxRaw, $discountRaw, $totalRaw) {
            if (!$this->invoice || !$this->invoice->exists) {
                $this->invoice = Invoice::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                    'client_id' => $this->clientId,
                    'status' => 'draft',
                    'tax' => $taxRaw,
                    'discount' => $discountRaw,
                    'total' => $totalRaw,
                    'issue_date' => $this->issueDate ?? now(),
                    'due_date' => $this->dueDate ?? now()->addDays(30),
                ]);
            } else {
                $this->invoice->update([
                    'client_id' => $this->clientId,
                    'tax' => $taxRaw,
                    'discount' => $discountRaw,
                    'total' => $totalRaw,
                    'issue_date' => $this->issueDate ?? now(),
                    'due_date' => $this->dueDate ?? now()->addDays(30),
                ]);
                // Delete existing items to recreate them
                $this->invoice->items()->delete();
            }

            foreach ($this->items as $index => $item) {
                $this->invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity'] * $item['unit_price'],
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('invoices.show', $this->invoice);
    }

    public function render()
    {
        return view('livewire.invoices.invoice-builder');
    }
}
