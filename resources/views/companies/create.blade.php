<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Company</h2>
            <a href="{{ route('companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Companies</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('companies.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
                @endif

                <!-- Company Details -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-[#2B333E]">Company Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                            <input type="text" name="industry" id="industry" value="{{ old('industry') }}" placeholder="e.g. Technology, Healthcare..."
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" name="website" id="website" value="{{ old('website') }}" placeholder="https://"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-1">Tax Number (NTN)</label>
                            <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-[#2B333E]">Address</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                            <textarea name="address" id="address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">{{ old('address') }}</textarea>
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="country" id="country" value="{{ old('country', 'Pakistan') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                        </div>
                    </div>
                </div>

                <!-- Additional -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-[#2B333E]">Additional Information</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-6">
                        <div>
                            <label for="account_manager_id" class="block text-sm font-medium text-gray-700 mb-1">Account Manager</label>
                            <select name="account_manager_id" id="account_manager_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                                <option value="">— Unassigned —</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('account_manager_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employee_code }} — {{ $employee->designation }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Internal notes about this company..."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('companies.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-[#2376D6] rounded-lg hover:bg-[#1d65b8] transition shadow-sm">
                        Create Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
