<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-brand-charcoal">Invoices</h1>
            <a href="{{ route('invoices.create') }}" wire:navigate class="bg-brand-blue text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Create Invoice</a>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by invoice number or company name..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-blue focus:ring-brand-blue">
            </div>
            <div class="w-full sm:w-64">
                <select wire:model.live="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-blue focus:ring-brand-blue">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Invoice List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse ($invoices as $invoice)
                    <li>
                        <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="block hover:bg-gray-50 transition">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-brand-blue truncate">{{ $invoice->invoice_number }}</p>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($invoice->status === 'paid') bg-green-100 text-green-800 
                                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800 
                                                @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="text-sm text-gray-900 font-semibold">${{ number_format($invoice->total, 2) }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {{ $invoice->client->company->name ?? 'Unknown Company' }}
                                        </p>
                                        @if($invoice->project)
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                {{ $invoice->project->name }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p>Due on <time datetime="{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}">{{ $invoice->due_date ? $invoice->due_date->format('M j, Y') : 'N/A' }}</time></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li>
                        <div class="px-4 py-8 sm:px-6 text-center text-gray-500">
                            No invoices found matching your criteria.
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
        
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
