<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Left Column: Chat/History -->
    <div class="md:col-span-2 flex flex-col h-[calc(100vh-8rem)]">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-[#2B333E]">#{{ $ticket->id }}: {{ $ticket->subject }}</h1>
            <p class="text-sm text-gray-500 mt-1">Reported by {{ optional($ticket->client)->name ?? 'Client' }} on {{ $ticket->created_at->format('M d, Y') }}</p>
        </div>
        
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto bg-gray-50 border border-gray-200 rounded-t-lg p-4 space-y-6">
            <!-- Initial Message as first bubble -->
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-[#2376D6] flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(optional($ticket->client)->name ?? 'C', 0, 2)) }}
                    </div>
                </div>
                <div class="flex-1 bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-[#2B333E]">{{ optional($ticket->client)->name ?? 'Client' }}</h4>
                        <span class="text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-gray-700 text-sm whitespace-pre-line">{{ $ticket->description }}</div>
                </div>
            </div>

            @foreach($messages as $msg)
                @if($msg->sender_type === 'employee')
                    <!-- Agent Reply -->
                    <div class="flex gap-4 flex-row-reverse">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-[#2B333E] flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(optional($msg->sender->user)->name ?? 'E', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-1 bg-blue-50 border border-blue-100 rounded-lg shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-bold text-[#2B333E]">{{ optional($msg->sender->user)->name ?? 'Support Agent' }}</h4>
                                <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-gray-700 text-sm whitespace-pre-line">{{ $msg->message }}</div>
                        </div>
                    </div>
                @else
                    <!-- Client Reply -->
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-[#2376D6] flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(optional($msg->sender)->name ?? 'C', 0, 2)) }}
                            </div>
                        </div>
                        <div class="flex-1 bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-bold text-[#2B333E]">{{ optional($msg->sender)->name ?? 'Client' }}</h4>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                                    @if($msg->logged_by_employee_id)
                                    <span class="text-[10px] text-gray-400 italic">Logged by {{ optional($msg->loggedByEmployee->user)->name ?? 'Agent' }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-gray-700 text-sm whitespace-pre-line">{{ $msg->message }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        
        <!-- Reply Box -->
        <div class="bg-white border-b border-l border-r border-gray-200 rounded-b-lg p-4">
            <form wire:submit="submitReply">
                @csrf
                <div class="mb-2">
                    <label for="replyMessage" class="sr-only">Your Reply</label>
                    <textarea wire:model="replyMessage" id="replyMessage" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]" placeholder="Type your reply here..."></textarea>
                    @error('replyMessage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <input wire:model="isProxyReply" id="isProxyReply" type="checkbox" class="h-4 w-4 text-[#2376D6] focus:ring-[#2376D6] border-gray-300 rounded">
                        <label for="isProxyReply" class="ml-2 block text-sm text-gray-700">
                            Log as Client Reply (Proxy)
                        </label>
                    </div>
                    <button type="submit" class="bg-[#2376D6] hover:bg-blue-600 text-white px-6 py-2 rounded shadow font-medium">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Metadata/Controls -->
    <div class="md:col-span-1">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-bold text-[#2B333E] mb-4 border-b pb-2">Ticket Details</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <select wire:model.change="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] text-[#2B333E]">
                        <option value="open">Open</option>
                        <option value="waiting_client">Waiting on Client</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Assignee</label>
                    <select wire:model.change="assignee" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] text-[#2B333E]">
                        <option value="">Unassigned</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ optional($emp->user)->name ?? $emp->employee_code }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Priority</label>
                    <div class="mt-1 flex items-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $ticket->priority === 'high' ? 'bg-red-100 text-red-800' : ($ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Customer</label>
                    <div class="text-[#2B333E] text-sm">
                        <p class="font-medium">{{ optional($ticket->client)->name ?? 'N/A' }}</p>
                        <p class="text-gray-500">{{ optional($ticket->client)->email ?? 'N/A' }}</p>
                        <p class="text-gray-500">{{ optional(optional($ticket->client)->company)->name ?? 'No Company' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
