<x-app-layout>


<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#2B333E]">Add New Employee</h1>
            <p class="text-sm text-gray-500 mt-1">Create an employee profile and user account in one step.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Back to Staff</a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-[#2B333E] mb-4">Account Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address (Login) *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Temporary Password *</label>
                        <input type="password" name="password" required minlength="8" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">System Role *</label>
                        <select name="role" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                            <option value="">— Select a Role —</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ ucwords(str_replace('-', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h3 class="text-lg font-semibold text-[#2B333E] mb-4">Employee Profile</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID / Code *</label>
                        <input type="text" name="employee_code" value="{{ old('employee_code') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Designation / Job Title *</label>
                        <input type="text" name="designation" value="{{ old('designation') }}" required placeholder="e.g. Senior Developer" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" value="{{ old('department') }}" placeholder="e.g. Sales, IT" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6]">
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-[#2376D6] text-white rounded-lg hover:bg-[#1a5bb0] shadow-sm font-medium transition">
                    Create Employee
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
