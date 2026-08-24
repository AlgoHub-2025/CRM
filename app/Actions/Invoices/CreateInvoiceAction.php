<?php
namespace App\Actions\Invoices;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateInvoiceAction
{
    public function execute(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            $invoice = Invoice::create([
                'invoice_number' => $data['invoice_number'] ?? 'INV-' . strtoupper(Str::random(6)),
                'client_id' => $data['client_id'],
                'project_id' => $data['project_id'] ?? null,
                'contract_id' => $data['contract_id'] ?? null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'status' => $data['status'] ?? 'draft',
            ]);

            $subtotal = 0;
            foreach ($items as $index => $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineTotal;

                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                    'sort_order' => $index,
                ]);
            }

            $total = $subtotal - $invoice->discount + $invoice->tax;

            $invoice->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            return $invoice;
        });
    }
}
