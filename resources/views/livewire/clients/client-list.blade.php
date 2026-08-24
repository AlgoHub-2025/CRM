<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#2B333E]">Clients</h2>
            <p class="text-sm text-[#6B7280] mt-1">Manage your converted clients</p>
        </div>
        <div>
            <!-- Clients are created automatically when an Opportunity is won -->
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-t-lg border border-[#E2E4E8] border-b-0 p-4">
        <div class="max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-[#E2E4E8] rounded-md leading-5 bg-white placeholder-[#6B7280] focus:outline-none focus:ring-1 focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm" placeholder="Search clients by company name...">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E2E4E8] rounded-b-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E2E4E8]">
                <thead class="bg-[#F4F4F5]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider cursor-pointer" wire:click="sortBy('company.name')">
                            Company
                            @if($sortField === 'company.name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">
                            Primary Contact
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                            Status
                            @if($sortField === 'status')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                            Client Since
                            @if($sortField === 'created_at')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[#E2E4E8]">
                    @forelse($clients as $client)
                    <tr class="hover:bg-[#F4F4F5] transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-[#2B333E]">{{ $client->company->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($client->primaryContact)
                                <div class="text-sm text-[#2B333E]">{{ $client->primaryContact->first_name }} {{ $client->primaryContact->last_name }}</div>
                                <div class="text-xs text-[#6B7280]">{{ $client->primaryContact->email }}</div>
                            @else
                                <span class="text-sm text-[#6B7280]">No primary contact</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($client->status === 'active')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">
                            {{ $client->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @can('view', $client)
                            <a href="{{ route('clients.show', $client) }}" class="text-[#2376D6] hover:text-blue-800 transition">View</a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#6B7280] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-[#2B333E]">No clients found</h3>
                            <p class="mt-1 text-sm text-[#6B7280]">Clients are created automatically when an Opportunity is won.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($clients->hasPages())
        <div class="px-6 py-3 bg-[#F4F4F5] border-t border-[#E2E4E8]">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
