<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[#2B333E]">Support Tickets</h1>
        <button wire:click="toggleCreateForm" class="bg-[#2376D6] hover:bg-blue-600 text-white px-4 py-2 rounded shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Ticket
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-4 items-end">
        <div class="w-full md:w-1/3">
            <label class="block text-sm font-medium text-[#2B333E] mb-1">Search</label>
            <input type="text" wire:model.live="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]" placeholder="Search tickets...">
        </div>
        <div class="w-full md:w-1/4">
            <label class="block text-sm font-medium text-[#2B333E] mb-1">Status</label>
            <select wire:model.live="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                <option value="">All Statuses</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="waiting_client">Waiting on Client</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
        <div class="w-full md:w-1/4">
            <label class="block text-sm font-medium text-[#2B333E] mb-1">Priority</label>
            <select wire:model.live="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
        </div>
    </div>

    <!-- Ticket List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#2B333E] uppercase tracking-wider">Ticket</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#2B333E] uppercase tracking-wider">Client</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#2B333E] uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#2B333E] uppercase tracking-wider">Priority</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#2B333E] uppercase tracking-wider">Assignee</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#2B333E] uppercase tracking-wider">Updated</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-[#2376D6]">{{ $ticket->subject }}</div>
                        <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 60) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $ticket->client?->company?->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($ticket->status === 'open') bg-green-100 text-green-800
                            @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($ticket->status === 'waiting_client') bg-yellow-100 text-yellow-800
                            @elseif($ticket->status === 'resolved') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($ticket->priority === 'critical') bg-red-100 text-red-800
                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $ticket->assignee?->user?->name ?? 'Unassigned' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <h3 class="mt-2 text-sm font-medium text-[#2B333E]">No tickets found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new ticket.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>

    <!-- Create Ticket Modal -->
    @if($showCreateForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="toggleCreateForm"></div>
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-semibold leading-6 text-[#2B333E] mb-4">Create Support Ticket</h3>
                    <p class="text-xs text-gray-500 mb-4">Creating on behalf of a client. Your employee ID will be recorded for audit.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client *</label>
                            <select wire:model="ticketClientId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                <option value="">Select a client...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->company?->name ?? $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Project (optional)</label>
                            <select wire:model="ticketProjectId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                <option value="">No specific project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject *</label>
                            <input wire:model="ticketSubject" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm" placeholder="Brief summary of the issue">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description *</label>
                            <textarea wire:model="ticketDescription" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm" placeholder="Client's original request (will become the first message)"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority *</label>
                            <select wire:model="ticketPriority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button wire:click="createTicket" type="button" class="inline-flex w-full justify-center rounded-md bg-[#2376D6] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto">Create Ticket</button>
                    <button wire:click="toggleCreateForm" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
