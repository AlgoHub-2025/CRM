<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lead->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">Lead Details</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(!$lead->converted_at)
                <form action="{{ route('leads.convert', $lead) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Convert to Opportunity</button>
                </form>
                @endif
                <a href="{{ route('leads.edit', $lead) }}" class="px-4 py-2 text-sm font-medium text-white bg-[#2376D6] rounded-lg hover:bg-[#1d65b8] transition">Edit Lead</a>
                <a href="{{ route('leads.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Contact Card -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-[#2B333E]">Contact Information</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($lead->priority === 'high') bg-red-50 text-red-700
                                @elseif($lead->priority === 'medium') bg-amber-50 text-amber-700
                                @else bg-emerald-50 text-emerald-700 @endif">
                                {{ ucfirst($lead->priority) }} Priority
                            </span>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Name</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->name }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Email</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->email ?? '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Phone</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->phone ?? '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">WhatsApp</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->whatsapp ?? '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Website</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->website ? $lead->website : '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Location</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->location ?? '—' }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <!-- Business Details Card -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-[#2B333E]">Business Details</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Company</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->company?->name ?? '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Industry</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->industry ?? '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Interested Service</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->interested_service ?? '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Estimated Budget</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->estimated_budget ? 'PKR ' . number_format($lead->estimated_budget) : '—' }}</dd></div>
                                <div><dt class="text-xs font-medium text-gray-500 uppercase">Lead Source</dt><dd class="mt-1 text-sm text-[#2B333E]">{{ $lead->source?->name ?? '—' }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    @if($lead->description)
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-[#2B333E]">Description</h3>
                        </div>
                        <div class="p-6 text-sm text-gray-700 whitespace-pre-wrap">{{ $lead->description }}</div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-[#2B333E] uppercase tracking-wider">Pipeline Status</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <span class="text-xs text-gray-500">Current Stage</span>
                                <div class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#2376D6]/10 text-[#2376D6]">
                                    {{ $lead->status?->name ?? 'Unknown' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Assigned To</span>
                                <p class="mt-1 text-sm font-medium text-[#2B333E]">{{ $lead->assignedTo?->designation ?? 'Unassigned' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Created</span>
                                <p class="mt-1 text-sm text-[#2B333E]">{{ $lead->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Last Updated</span>
                                <p class="mt-1 text-sm text-[#2B333E]">{{ $lead->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-white rounded-lg shadow-sm border border-red-100">
                        <div class="px-6 py-4 border-b border-red-100">
                            <h3 class="text-sm font-semibold text-red-700 uppercase tracking-wider">Danger Zone</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead? This action cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                    Delete this Lead
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
