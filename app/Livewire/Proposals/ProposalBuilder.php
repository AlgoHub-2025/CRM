<?php

namespace App\Livewire\Proposals;

use App\Actions\Proposals\CreateProposalAction;
use App\Models\Client;
use App\Models\Opportunity;
use Livewire\Component;

class ProposalBuilder extends Component
{
    public $client_id;
    public $opportunity_id;
    public $title;
    public $valid_until;
    public $payment_terms;
    
    public $items = [];
    public $discount = 0;
    public $tax_rate = 0;

    public function mount(Opportunity $opportunity = null, \App\Models\Proposal $proposal = null)
    {
        if ($opportunity && $opportunity->exists) {
            $this->opportunity_id = $opportunity->id;
            $this->client_id = $opportunity->client_id ?? null;
        }

        if ($proposal && $proposal->exists) {
            $this->opportunity_id = $proposal->opportunity_id;
            $this->client_id = $proposal->client_id;
            $this->title = $proposal->project_title;
            $this->valid_until = $proposal->valid_until ? $proposal->valid_until->format('Y-m-d') : null;
            $this->payment_terms = $proposal->payment_terms;
            $this->discount = $proposal->discount;
            $this->tax_rate = $proposal->tax > 0 ? ($proposal->tax / ($proposal->subtotal - $proposal->discount)) * 100 : 0;
            
            if ($proposal->items) {
                $this->items = $proposal->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price
                    ];
                })->toArray();
            }
        }

        if (empty($this->items)) {
            $this->items = [
                ['description' => '', 'quantity' => 1, 'unit_price' => 0]
            ];
        }
    }

    public function addItem()
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updateOrder($orderedIndexes)
    {
        $newItems = [];
        foreach ($orderedIndexes as $index) {
            if (isset($this->items[$index])) {
                $newItems[] = $this->items[$index];
            }
        }
        $this->items = array_values($newItems);
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'valid_until' => 'nullable|date',
            'payment_terms' => 'nullable|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01|max:9999999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999999',
            'discount' => 'nullable|numeric|min:0|max:999999999',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += ($item['quantity'] * $item['unit_price']);
        }
        
        $discountAmount = $this->discount;
        $taxAmount = ($subtotal - $discountAmount) * ($this->tax_rate / 100);
        $total = $subtotal - $discountAmount + $taxAmount;

        $data = [
            'project_title' => $this->title,
            'client_id' => $this->client_id,
            'opportunity_id' => $this->opportunity_id,
            'valid_until' => $this->valid_until,
            'payment_terms' => $this->payment_terms,
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'tax' => $taxAmount,
            'total' => $total,
            'status' => 'draft',
        ];

        if (class_exists(CreateProposalAction::class)) {
            $proposal = app(CreateProposalAction::class)->execute($data, $this->items);
            return redirect()->route('proposals.show', $proposal);
        } else {
            return redirect()->route('proposals.index');
        }
    }

    public function render()
    {
        return view('livewire.proposals.proposal-builder', [
            'clients' => class_exists(Client::class) ? Client::all() : [],
            'opportunities' => class_exists(Opportunity::class) ? Opportunity::all() : [],
        ]);
    }
}
