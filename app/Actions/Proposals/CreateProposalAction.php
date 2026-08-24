<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateProposalAction
{
    /**
     * Create a new proposal with line items, calculating totals automatically.
     */
    public function execute(array $data, array $itemsData): Proposal
    {
        if (empty($data['client_id']) && empty($data['opportunity_id'])) {
            throw ValidationException::withMessages([
                'client_id' => 'A proposal must be linked to either a client or an opportunity.',
            ]);
        }

        return DB::transaction(function () use ($data, $itemsData) {
            // Generate proposal number if not provided
            if (empty($data['proposal_number'])) {
                $data['proposal_number'] = 'PRP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }

            // Remove totals from incoming data as we will calculate them
            unset($data['subtotal'], $data['total']);

            $proposal = Proposal::create($data);

            $subtotal = 0;
            $sortOrder = 1;

            foreach ($itemsData as $itemData) {
                $quantity = $itemData['quantity'] ?? 1;
                $unitPrice = $itemData['unit_price'] ?? 0;
                $lineTotal = $quantity * $unitPrice;

                $proposal->items()->create([
                    'description' => $itemData['description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'sort_order' => $itemData['sort_order'] ?? $sortOrder++,
                ]);

                $subtotal += $lineTotal;
            }

            $discount = $data['discount'] ?? 0;
            $tax = $data['tax'] ?? 0; // assuming tax is a flat amount for now, could be percentage later
            $total = $subtotal - $discount + $tax;

            $proposal->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            return $proposal;
        });
    }
}
