<div>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-[#2B333E] sm:truncate sm:text-3xl sm:tracking-tight">
                    {{ $project->name }}
                </h2>
                <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                        </svg>
                        Deadline: {{ $project->deadline ? $project->deadline->format('M d, Y') : 'N/A' }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        Budget: ${{ number_format($project->budget, 2) }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-[#2376D6] ring-1 ring-inset ring-blue-700/10">
                            {{ str_replace('_', ' ', Str::title($project->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Edit Project
                </button>
            </div>
        </div>
        
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="col-span-1 space-y-6">
                <!-- Details Card -->
                <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-[#2B333E]">Project Details</h3>
                        <div class="mt-4 border-t border-gray-100">
                            <dl class="divide-y divide-gray-100">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-[#2B333E]">Client</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $project->client?->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-[#2B333E]">Manager</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $project->projectManager?->user?->name ?? 'Unassigned' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-[#2B333E]">Technology</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $project->technology ?? 'N/A' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-[#2B333E]">Description</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0 whitespace-pre-wrap">{{ $project->description ?? 'No description provided.' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-1 lg:col-span-2 space-y-6">
                <!-- Milestones -->
                <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-base font-semibold leading-6 text-[#2B333E]">Milestones</h3>
                            <button wire:click="toggleMilestoneForm" class="inline-flex items-center rounded-md bg-[#2376D6] px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Milestone
                            </button>
                        </div>

                        @if($showMilestoneForm)
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input wire:model="milestoneName" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm" placeholder="Milestone name">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                    <input wire:model="milestoneDueDate" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                </div>
                            </div>
                            <div class="mt-3 flex justify-end space-x-2">
                                <button wire:click="toggleMilestoneForm" class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                                <button wire:click="createMilestone" class="rounded-md bg-[#2376D6] px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Create</button>
                            </div>
                        </div>
                        @endif

                        @if($project->milestones->count())
                        <ul class="divide-y divide-gray-100">
                            @foreach($project->milestones->sortBy('order') as $milestone)
                            <li class="flex items-center justify-between py-3">
                                <div>
                                    <p class="text-sm font-medium text-[#2B333E]">{{ $milestone->name }}</p>
                                    <p class="text-xs text-gray-500">Due: {{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'No date set' }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                    {{ $milestone->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                    {{ ucfirst($milestone->status) }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-sm text-gray-500 italic">No milestones created yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Task Board component inclusion -->
                <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-[#2B333E] mb-4">Task Board</h3>
                        <livewire:projects.task-board :project="$project" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
