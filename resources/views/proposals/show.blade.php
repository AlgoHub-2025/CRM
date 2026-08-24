<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between print:hidden">
            <div>
                <a href="{{ route('proposals.index') }}" class="text-sm font-medium text-brand-blue hover:text-brand-blue">
                    &larr; Back to Proposals
                </a>
            </div>
            <div class="flex items-center space-x-3">
                @if($proposal->status !== 'accepted')
                <form action="{{ route('proposals.accept', $proposal) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-x-1.5 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                        <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                        Accept &amp; Draft Contract
                    </button>
                </form>
                @endif
                @if($proposal->status === 'draft')
                <form action="{{ route('proposals.send', $proposal) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-x-1.5 rounded-md bg-brand-blue px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue">
                        <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                            <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                        </svg>
                        Send to Client
                    </button>
                </form>
                @endif
                <button onclick="window.print()" class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0118 8.653v4.083A2.25 2.25 0 0115.75 15H14v1.25c0 .966-.784 1.75-1.75 1.75h-4.5A1.75 1.75 0 016 16.25V15H4.25A2.25 2.25 0 012 12.736V8.653c0-1.082.78-2.03 1.874-2.198.374-.056.75-.107 1.127-.153V2.75zm1.5 0v3.361c1-.1 2.012-.161 3.037-.161 1.025 0 2.037.061 3.037.161V2.75a.25.25 0 00-.25-.25h-6.5a.25.25 0 00-.25.25zm-1 9.5a.75.75 0 100-1.5.75.75 0 000 1.5zm.75-6.75a1 1 0 00-1 1v4.083a.75.75 0 00.75.75h10.5a.75.75 0 00.75-.75V6.5a1 1 0 00-1-1c-.933-.14-1.875-.24-2.825-.296V6.75a.75.75 0 01-1.5 0V5.132a48.514 48.514 0 00-4.9-.036v1.654a.75.75 0 01-1.5 0V5.204c-.95.056-1.892.156-2.825.296zM12.5 16.25v-3.5h-5v3.5a.25.25 0 00.25.25h4.5a.25.25 0 00.25-.25z" clip-rule="evenodd" />
                    </svg>
                    Print to PDF
                </button>
            </div>
        </div>

        <!-- Document area -->
        <div class="bg-white shadow-xl sm:rounded-lg print:shadow-none print:rounded-none overflow-hidden">
            <div class="px-8 py-10 sm:px-12 sm:py-16 text-gray-900">
                <!-- Header -->
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-brand-charcoal">AlgoHub CRM</h1>
                        <p class="mt-2 text-sm text-gray-500">
                            123 Business Avenue<br>
                            Suite 100<br>
                            City, State 12345<br>
                            contact@algohub.com
                        </p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-2xl font-semibold text-gray-400 uppercase tracking-wider">Proposal</h2>
                        <div class="mt-4 text-sm">
                            <p class="font-medium text-brand-charcoal">Proposal #: <span class="font-normal text-gray-600">{{ $proposal->proposal_number }}</span></p>
                            <p class="font-medium text-brand-charcoal mt-1">Date: <span class="font-normal text-gray-600">{{ $proposal->created_at->format('M d, Y') }}</span></p>
                            <p class="font-medium text-brand-charcoal mt-1">Valid Until: <span class="font-normal text-gray-600">{{ $proposal->valid_until ? $proposal->valid_until->format('M d, Y') : 'N/A' }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Client Info & Title -->
                <div class="mt-12 flex justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-brand-charcoal uppercase tracking-wide">Prepared For</h3>
                        <div class="mt-2 text-sm text-gray-700">
                            @if($proposal->client)
                                <p class="font-bold text-brand-charcoal">{{ $proposal->client->name }}</p>
                                @if($proposal->client->company)
                                    <p>{{ $proposal->client->company->name }}</p>
                                @endif
                                <p class="mt-1">{{ $proposal->client->email }}</p>
                            @elseif($proposal->opportunity)
                                <p class="font-bold text-brand-charcoal">{{ optional($proposal->opportunity->lead)->name ?? 'Opportunity Client' }}</p>
                                @if(optional($proposal->opportunity->lead)->company)
                                    <p>{{ $proposal->opportunity->lead->company->name }}</p>
                                @endif
                            @else
                                <p>N/A</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="max-w-sm text-right">
                        <h3 class="text-sm font-semibold text-brand-charcoal uppercase tracking-wide">Project</h3>
                        <p class="mt-2 text-lg text-gray-900 font-medium">{{ $proposal->title }}</p>
                        @if($proposal->opportunity)
                        <p class="mt-1 text-sm text-gray-600">Re: {{ $proposal->opportunity->title }}</p>
                        @endif
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="mt-12">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="border-b border-gray-300">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-brand-charcoal sm:pl-0">Description</th>
                                <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-brand-charcoal sm:table-cell">Quantity</th>
                                <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-brand-charcoal sm:table-cell">Unit Price</th>
                                <th scope="col" class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-brand-charcoal sm:pr-0">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 border-b border-gray-200">
                            @forelse($proposal->items ?? [] as $item)
                                <tr>
                                    <td class="py-4 pl-4 pr-3 text-sm sm:pl-0">
                                        <div class="font-medium text-gray-900">{{ $item->description ?? $item['description'] }}</div>
                                    </td>
                                    <td class="hidden px-3 py-4 text-right text-sm text-gray-500 sm:table-cell">{{ $item->quantity ?? $item['quantity'] }}</td>
                                    <td class="hidden px-3 py-4 text-right text-sm text-gray-500 sm:table-cell">${{ number_format($item->unit_price ?? $item['unit_price'], 2) }}</td>
                                    <td class="py-4 pl-3 pr-4 text-right text-sm text-gray-900 sm:pr-0">${{ number_format(($item->quantity ?? $item['quantity']) * ($item->unit_price ?? $item['unit_price']), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-sm text-gray-500">No items on this proposal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-6 text-right text-sm font-normal text-gray-500 sm:table-cell sm:pl-0">Subtotal</th>
                                <th scope="row" class="pl-4 pr-3 pt-6 text-left text-sm font-normal text-gray-500 sm:hidden">Subtotal</th>
                                <td class="pl-3 pr-4 pt-6 text-right text-sm text-gray-900 sm:pr-0">${{ number_format($proposal->subtotal, 2) }}</td>
                            </tr>
                            @if($proposal->discount > 0)
                            <tr>
                                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-normal text-gray-500 sm:table-cell sm:pl-0">Discount</th>
                                <th scope="row" class="pl-4 pr-3 pt-4 text-left text-sm font-normal text-gray-500 sm:hidden">Discount</th>
                                <td class="pl-3 pr-4 pt-4 text-right text-sm text-gray-900 sm:pr-0">-${{ number_format($proposal->discount, 2) }}</td>
                            </tr>
                            @endif
                            @if($proposal->tax > 0)
                            <tr>
                                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-normal text-gray-500 sm:table-cell sm:pl-0">Tax</th>
                                <th scope="row" class="pl-4 pr-3 pt-4 text-left text-sm font-normal text-gray-500 sm:hidden">Tax</th>
                                <td class="pl-3 pr-4 pt-4 text-right text-sm text-gray-900 sm:pr-0">${{ number_format($proposal->tax, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-base font-semibold text-brand-charcoal sm:table-cell sm:pl-0">Total</th>
                                <th scope="row" class="pl-4 pr-3 pt-4 text-left text-base font-semibold text-brand-charcoal sm:hidden">Total</th>
                                <td class="pl-3 pr-4 pt-4 text-right text-base font-semibold text-brand-charcoal sm:pr-0">${{ number_format($proposal->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Payment Terms / Footer -->
                @if($proposal->payment_terms)
                <div class="mt-16 pt-8 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-brand-charcoal">Payment Terms & Notes</h4>
                    <p class="mt-2 text-sm text-gray-600 whitespace-pre-line">{{ $proposal->payment_terms }}</p>
                </div>
                @endif
                
                <div class="mt-16 pt-8 text-center text-xs text-gray-400">
                    Thank you for your business. For any questions, please contact us.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add print CSS -->
    <style>
        @media print {
            body {
                background-color: white !important;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:shadow-none {
                box-shadow: none !important;
            }
            .print\:rounded-none {
                border-radius: 0 !important;
            }
            /* Hide headers, navbars or footers specific to x-app-layout if possible */
            header, nav, aside {
                display: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</x-app-layout>

