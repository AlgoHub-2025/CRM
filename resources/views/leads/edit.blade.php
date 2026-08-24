<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Lead: {{ $lead->name }}</h2>
            <a href="{{ route('leads.show', $lead) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Lead</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('leads.update', $lead) }}" method="POST">
                @csrf @method('PUT')

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
                @endif

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-semibold text-[#2B333E]">Contact Information</h3></div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $lead->name) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $lead->phone) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $lead->whatsapp) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $lead->location) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                    </div>
                </div>

                <!-- Business Details -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-semibold text-[#2B333E]">Business Details</h3></div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <select name="company_id" id="company_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                                <option value="">— No company —</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id', $lead->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                            <input type="text" name="industry" id="industry" value="{{ old('industry', $lead->industry) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="source_id" class="block text-sm font-medium text-gray-700 mb-1">Lead Source</label>
                            <select name="source_id" id="source_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                                <option value="">— Select —</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('source_id', $lead->source_id) == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="interested_service" class="block text-sm font-medium text-gray-700 mb-1">Interested Service</label>
                            <input type="text" name="interested_service" id="interested_service" value="{{ old('interested_service', $lead->interested_service) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="estimated_budget" class="block text-sm font-medium text-gray-700 mb-1">Estimated Budget (PKR)</label>
                            <input type="number" name="estimated_budget" id="estimated_budget" value="{{ old('estimated_budget', $lead->estimated_budget) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                    </div>
                </div>

                <!-- Pipeline & Assignment -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-semibold text-[#2B333E]">Pipeline & Assignment</h3></div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status_id" id="status_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ old('status_id', $lead->status_id) == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                            <select name="assigned_to" id="assigned_to" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                                <option value="">— Unassigned —</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('assigned_to', $lead->assigned_to) == $employee->id ? 'selected' : '' }}>{{ $employee->employee_code }} — {{ $employee->designation }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4" x-data="{ priority: '{{ old('priority', $lead->priority) }}' }">
                                <label for="priority_low"
                                    class="inline-flex items-center px-3 py-2 rounded-lg border cursor-pointer transition select-none"
                                    :class="priority === 'low' ? 'border-[#10b981] bg-[#ecfdf5] text-[#047857]' : 'border-gray-200 bg-white text-gray-700'">
                                    <input type="radio" name="priority" id="priority_low" value="low" x-model="priority" class="hidden">
                                    <span class="w-2 h-2 rounded-full mr-2 bg-[#10b981]"></span>
                                    <span class="text-sm font-medium">Low</span>
                                </label>
                                <label for="priority_medium"
                                    class="inline-flex items-center px-3 py-2 rounded-lg border cursor-pointer transition select-none"
                                    :class="priority === 'medium' ? 'border-[#f59e0b] bg-[#fffbeb] text-[#b45309]' : 'border-gray-200 bg-white text-gray-700'">
                                    <input type="radio" name="priority" id="priority_medium" value="medium" x-model="priority" class="hidden">
                                    <span class="w-2 h-2 rounded-full mr-2 bg-[#f59e0b]"></span>
                                    <span class="text-sm font-medium">Medium</span>
                                </label>
                                <label for="priority_high"
                                    class="inline-flex items-center px-3 py-2 rounded-lg border cursor-pointer transition select-none"
                                    :class="priority === 'high' ? 'border-[#ef4444] bg-[#fef2f2] text-[#b91c1c]' : 'border-gray-200 bg-white text-gray-700'">
                                    <input type="radio" name="priority" id="priority_high" value="high" x-model="priority" class="hidden">
                                    <span class="w-2 h-2 rounded-full mr-2 bg-[#ef4444]"></span>
                                    <span class="text-sm font-medium">High</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-semibold text-[#2B333E]">Additional Notes</h3></div>
                    <div class="p-6">
                        <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">{{ old('description', $lead->description) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('leads.show', $lead) }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-[#2376D6] rounded-lg hover:bg-[#1d65b8] transition shadow-sm">Update Lead</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
