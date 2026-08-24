<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-brand-charcoal">Contracts</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all contracts including their number, client, value, dates, and status.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-brand-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 sm:w-auto">
                Create Contract
            </button>
        </div>
    </div>
    
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-brand-charcoal sm:pl-6">Contract Number</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Client</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Value</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Start Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">End Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($contracts as $contract)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-brand-blue sm:pl-6">
                                        <a href="{{ route('contracts.show', $contract) }}" class="hover:text-brand-blue">{{ $contract->contract_number ?? 'N/A' }}</a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div class="text-brand-charcoal font-medium">{{ $contract->client->name ?? 'Unknown Client' }}</div>
                                        <div class="text-gray-500 text-xs">{{ $contract->client->company->name ?? '' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">
                                        ${{ number_format($contract->value ?? 0, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $contract->start_date ? (is_string($contract->start_date) ? \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') : $contract->start_date->format('M d, Y')) : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $contract->end_date ? (is_string($contract->end_date) ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : $contract->end_date->format('M d, Y')) : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @php
                                            $status = strtolower($contract->status ?? 'pending');
                                            $badgeClasses = match($status) {
                                                'active' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-blue-100 text-blue-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'expired' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-yellow-100 text-yellow-800',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $badgeClasses }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('contracts.show', $contract) }}" class="text-brand-blue hover:text-brand-blue font-semibold">View<span class="sr-only">, {{ $contract->contract_number }}</span></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="whitespace-nowrap py-10 px-4 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-gray-500 font-medium">No contracts found.</p>
                                            <p class="text-gray-400 text-xs mt-1">Get started by creating a new contract.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
</div>

