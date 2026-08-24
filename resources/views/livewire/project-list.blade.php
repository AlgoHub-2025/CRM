<div>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-[#2B333E]">Projects</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all projects including their name, client, status, and deadline.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="#" class="block rounded-md bg-[#2376D6] px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-[#1d63b8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2376D6]">Add Project</a>
            </div>
        </div>
        
        <div class="mt-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="w-full sm:max-w-xs">
                <label for="search" class="sr-only">Search projects</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live="search" type="text" name="search" id="search" class="block w-full rounded-md border-0 py-1.5 pl-10 text-[#2B333E] ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-[#2376D6] sm:text-sm sm:leading-6" placeholder="Search projects...">
                </div>
            </div>
            
            <div class="w-full sm:max-w-xs">
                <select wire:model.live="status" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-[#2B333E] ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-[#2376D6] sm:text-sm sm:leading-6">
                    <option value="">All Statuses</option>
                    <option value="not_started">Not Started</option>
                    <option value="planning">Planning</option>
                    <option value="development">Development</option>
                    <option value="testing">Testing</option>
                    <option value="client_review">Client Review</option>
                    <option value="revision">Revision</option>
                    <option value="completed">Completed</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-[#2B333E] sm:pl-6">Name</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-[#2B333E]">Client</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-[#2B333E]">Manager</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-[#2B333E]">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-[#2B333E]">Deadline</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">View</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($projects as $project)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-[#2B333E] sm:pl-6">{{ $project->name }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $project->client?->name ?? 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $project->projectManager?->user?->name ?? 'Unassigned' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-[#2376D6] ring-1 ring-inset ring-blue-700/10">
                                                {{ str_replace('_', ' ', Str::title($project->status)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $project->deadline ? $project->deadline->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <a href="#" class="text-[#2376D6] hover:text-[#1d63b8]">View<span class="sr-only">, {{ $project->name }}</span></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-semibold text-[#2B333E]">No projects found</h3>
                                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</div>
