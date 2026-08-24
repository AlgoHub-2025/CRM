<?php

namespace App\Livewire\Contracts;

use App\Models\Contract;
use Livewire\Component;
use Livewire\WithPagination;

class ContractList extends Component
{
    use WithPagination;

    public function render()
    {
        $contracts = Contract::with(['client.company'])->latest()->paginate(10);
        
        return view('livewire.contracts.contract-list', [
            'contracts' => $contracts
        ]);
    }
}
