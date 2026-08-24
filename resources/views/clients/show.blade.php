<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-[#2B333E]">{{ $client->company->name ?? 'Unknown Company' }}</h2>
    </x-slot>

    <div class="p-6">
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-[#E2E4E8]">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-[#2B333E]">
                        Client Profile
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-[#6B7280]">
                        Details and associated records for this client.
                    </p>
                </div>
                <div>
                    @if($client->status === 'active')
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="border-t border-[#E2E4E8]">
                <dl>
                    <div class="bg-[#F4F4F5] px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-[#6B7280]">
                            Company Name
                        </dt>
                        <dd class="mt-1 text-sm text-[#2B333E] sm:mt-0 sm:col-span-2 font-semibold">
                            <a href="{{ route('companies.show', $client->company_id) }}" class="text-[#2376D6] hover:underline">
                                {{ $client->company->name ?? 'N/A' }}
                            </a>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-[#6B7280]">
                            Primary Contact
                        </dt>
                        <dd class="mt-1 text-sm text-[#2B333E] sm:mt-0 sm:col-span-2">
                            @if($client->primaryContact)
                                {{ $client->primaryContact->first_name }} {{ $client->primaryContact->last_name }} 
                                <span class="text-[#6B7280]">({{ $client->primaryContact->email }})</span>
                            @else
                                <span class="text-[#6B7280] italic">No primary contact assigned</span>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-[#F4F4F5] px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-[#6B7280]">
                            Client Since
                        </dt>
                        <dd class="mt-1 text-sm text-[#2B333E] sm:mt-0 sm:col-span-2">
                            {{ $client->created_at->format('F j, Y, g:i a') }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-[#6B7280]">
                            Converted From Opportunity
                        </dt>
                        <dd class="mt-1 text-sm text-[#2B333E] sm:mt-0 sm:col-span-2">
                            @if($client->convertedFromOpportunity)
                                <a href="{{ route('opportunities.index') }}" class="text-[#2376D6] hover:underline">
                                    {{ $client->convertedFromOpportunity->title }}
                                </a>
                            @else
                                <span class="text-[#6B7280]">N/A</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Placeholder for Future Modules -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-[#E2E4E8] p-6">
                <h3 class="text-lg font-medium text-[#2B333E] mb-4">Proposals & Contracts</h3>
                <p class="text-sm text-[#6B7280]">
                    Coming in future modules. This section will list all proposals and signed contracts linked to this client.
                </p>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-[#E2E4E8] p-6">
                <h3 class="text-lg font-medium text-[#2B333E] mb-4">Projects & Support</h3>
                <p class="text-sm text-[#6B7280]">
                    Coming in future modules. This section will list active projects and open support tickets.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
