<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#2B333E]">Leads</h2>
            <p class="text-sm text-[#6B7280] mt-1">Manage your sales leads</p>
        </div>
        <a href="{{ route('leads.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#2376D6] text-white text-sm font-medium rounded-lg hover:bg-[#1d65b8] transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Lead
        </a>
    </div>

    <div class="bg-white rounded-lg border border-[#E2E4E8]">
        <div class="p-4 border-b border-[#E2E4E8]">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search leads..."
                   class="w-full sm:w-1/3 px-4 py-2 text-sm border border-[#E2E4E8] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2376D6] focus:border-[#2376D6]">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E2E4E8]">
                <thead class="bg-[#F4F4F5]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#6B7280] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E4E8]">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-[#F4F4F5] transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#2B333E]">{{ $lead->name }}</div>
                                @if($lead->email)
                                    <div class="text-xs text-[#6B7280]">{{ $lead->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">
                                {{ $lead->company ? $lead->company->name : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#2376D6]/10 text-[#2376D6]">
                                    {{ $lead->status ? $lead->status->name : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($lead->priority === 'high') bg-[#DC2626]/10 text-[#DC2626]
                                    @elseif($lead->priority === 'medium') bg-[#D97706]/10 text-[#D97706]
                                    @else bg-[#16A34A]/10 text-[#16A34A] @endif">
                                    {{ ucfirst($lead->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('leads.show', $lead) }}" class="text-[#2376D6] hover:underline mr-3">View</a>
                                <a href="{{ route('leads.edit', $lead) }}" class="text-[#2B333E] hover:underline mr-3">Edit</a>
                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lead?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#DC2626] hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                <h3 class="mt-4 text-sm font-semibold text-[#2B333E]">No leads yet</h3>
                                <p class="mt-1 text-sm text-[#6B7280]">Create your first lead to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#E2E4E8]">
            {{ $leads->links() }}
        </div>
    </div>
</div>
