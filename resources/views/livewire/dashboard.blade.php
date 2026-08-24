<div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg border border-[#E2E4E8] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase tracking-wider">Total Leads</p>
                    <p class="text-3xl font-bold text-[#2B333E] mt-1 tabular-nums">{{ $totalLeads }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <svg class="w-6 h-6 text-[#2376D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#E2E4E8] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase tracking-wider">New Leads</p>
                    <p class="text-3xl font-bold text-[#2B333E] mt-1 tabular-nums">{{ $newLeads }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <svg class="w-6 h-6 text-[#16A34A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#E2E4E8] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase tracking-wider">Companies</p>
                    <p class="text-3xl font-bold text-[#2B333E] mt-1 tabular-nums">{{ $totalCompanies }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-full">
                    <svg class="w-6 h-6 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#E2E4E8] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase tracking-wider">Pipeline Value</p>
                    <p class="text-3xl font-bold text-[#2B333E] mt-1 tabular-nums">PKR {{ number_format($pipelineValue) }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('leads.create') }}" class="inline-flex items-center px-4 py-2 bg-[#2376D6] text-white text-sm font-medium rounded-lg hover:bg-[#1d65b8] transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Lead
        </a>
        <a href="{{ route('companies.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-[#2B333E] text-sm font-medium rounded-lg hover:bg-[#F4F4F5] transition shadow-sm border border-[#E2E4E8]">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Company
        </a>
    </div>

    <!-- Recent Leads -->
    <div class="bg-white rounded-lg border border-[#E2E4E8]">
        <div class="px-6 py-4 border-b border-[#E2E4E8]">
            <h3 class="text-base font-semibold text-[#2B333E]">Recent Leads</h3>
        </div>
        @if($recentLeads->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E4E8]">
                    <thead class="bg-[#F4F4F5]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6B7280] uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E4E8]">
                        @foreach($recentLeads as $lead)
                        <tr class="hover:bg-[#F4F4F5] transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('leads.show', $lead) }}" class="text-sm font-medium text-[#2B333E] hover:text-[#2376D6]">{{ $lead->name }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">{{ $lead->company?->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#2376D6]/10 text-[#2376D6]">{{ $lead->status?->name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($lead->priority === 'high') bg-[#DC2626]/10 text-[#DC2626]
                                    @elseif($lead->priority === 'medium') bg-[#D97706]/10 text-[#D97706]
                                    @else bg-[#16A34A]/10 text-[#16A34A] @endif">
                                    {{ ucfirst($lead->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">{{ $lead->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h3 class="mt-4 text-sm font-semibold text-[#2B333E]">No leads yet</h3>
                <p class="mt-1 text-sm text-[#6B7280]">Get started by creating your first lead.</p>
                <a href="{{ route('leads.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-[#2376D6] text-white text-sm font-medium rounded-lg hover:bg-[#1d65b8] transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Lead
                </a>
            </div>
        @endif
    </div>
</div>
