<div>
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-brand-charcoal">Proposals</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all proposals including their title, client, status, and total amount.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('proposals.create') }}" class="inline-flex items-center justify-center rounded-md bg-brand-blue px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 sm:w-auto">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                New Proposal
            </a>
        </div>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-brand-charcoal sm:pl-6">Proposal Number</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Client / Opportunity</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Title</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Valid Until</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-brand-charcoal">Total</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($proposals as $proposal)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-brand-charcoal sm:pl-6">
                                        {{ $proposal->proposal_number }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($proposal->client)
                                            <div class="text-brand-charcoal">{{ $proposal->client->name }}</div>
                                            <div class="text-gray-500">{{ optional($proposal->client->company)->name }}</div>
                                        @elseif($proposal->opportunity)
                                            <div class="text-brand-charcoal">{{ optional($proposal->opportunity->lead)->name ?? 'Opportunity' }}</div>
                                            <div class="text-gray-500">{{ optional(optional($proposal->opportunity->lead)->company)->name }}</div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $proposal->title }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset 
                                            @if($proposal->status === 'draft') bg-gray-50 text-gray-600 ring-gray-500/10 
                                            @elseif($proposal->status === 'sent') bg-blue-50 text-blue-700 ring-blue-700/10 
                                            @elseif($proposal->status === 'accepted') bg-green-50 text-green-700 ring-green-600/20 
                                            @elseif($proposal->status === 'declined') bg-red-50 text-red-700 ring-red-600/10 
                                            @else bg-gray-50 text-gray-600 ring-gray-500/10 @endif">
                                            {{ ucfirst($proposal->status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $proposal->valid_until ? $proposal->valid_until->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-brand-charcoal font-medium">
                                        ${{ number_format($proposal->total, 2) }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('proposals.show', $proposal) }}" class="text-brand-blue hover:text-brand-blue mr-3">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No proposals</h3>
                                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new proposal.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('proposals.create') }}" class="inline-flex items-center rounded-md bg-brand-blue px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue">
                                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                                </svg>
                                                New Proposal
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $proposals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

