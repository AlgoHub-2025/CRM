<div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    @php
        $statuses = [
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'completed' => 'Completed',
            'blocked' => 'Blocked'
        ];
    @endphp

    <div class="flex space-x-4 overflow-x-auto pb-4" 
         x-data="taskBoardSortable()" x-init="initSortable()">
        
        @foreach($statuses as $statusKey => $statusLabel)
            <div class="flex-shrink-0 w-80 bg-gray-50 rounded-lg p-4 flex flex-col" data-status="{{ $statusKey }}">
                
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-[#2B333E]">{{ $statusLabel }}</h4>
                    <span class="inline-flex items-center rounded-full bg-gray-200 px-2 py-1 text-xs font-medium text-gray-700">
                        {{ collect($tasks)->where('status', $statusKey)->count() }}
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto space-y-3 min-h-[150px] sortable-column" data-status="{{ $statusKey }}">
                    @foreach(collect($tasks)->where('status', $statusKey) as $task)
                        <div data-task-id="{{ $task->id }}"
                             class="bg-white p-4 rounded-md shadow-sm border border-gray-200 cursor-move hover:border-[#2376D6] transition-colors sortable-card">
                            <div class="flex justify-between items-start mb-2">
                                <h5 class="text-sm font-medium text-[#2B333E] line-clamp-2">{{ $task->title }}</h5>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($task->assignedTo)
                                        <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center text-xs font-medium text-[#2376D6]" title="{{ $task->assignedTo->user?->name }}">
                                            {{ substr($task->assignedTo->user?->name ?? 'U', 0, 1) }}
                                        </div>
                                    @else
                                        <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-500" title="Unassigned">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center text-xs text-gray-500">
                                    <svg class="mr-1 h-4 w-4 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $task->deadline ? $task->deadline->format('M d') : 'No date' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($statusKey === 'todo')
                <button wire:click="toggleTaskForm" type="button" class="mt-4 flex items-center text-sm font-medium text-gray-500 hover:text-[#2376D6]">
                    <svg class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add task
                </button>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Task Creation Modal -->
    @if($showTaskForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="toggleTaskForm"></div>
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-semibold leading-6 text-[#2B333E] mb-4">Add New Task</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title *</label>
                            <input wire:model="taskTitle" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm" placeholder="Task title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="taskDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm" placeholder="Task description"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority *</label>
                                <select wire:model="taskPriority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deadline</label>
                                <input wire:model="taskDeadline" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assign To</label>
                                <select wire:model="taskAssignedTo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->designation }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Milestone</label>
                                <select wire:model="taskMilestoneId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#2376D6] focus:ring-[#2376D6] sm:text-sm">
                                    <option value="">None</option>
                                    @foreach($milestones as $milestone)
                                        <option value="{{ $milestone->id }}">{{ $milestone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button wire:click="createTask" type="button" class="inline-flex w-full justify-center rounded-md bg-[#2376D6] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto">Create Task</button>
                    <button wire:click="toggleTaskForm" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        function taskBoardSortable() {
            return {
                initSortable() {
                    const columns = document.querySelectorAll('.sortable-column');
                    columns.forEach(col => {
                        new Sortable(col, {
                            group: 'shared',
                            animation: 150,
                            ghostClass: 'bg-blue-50',
                            onEnd: (evt) => {
                                const taskId = evt.item.dataset.taskId;
                                const toColumn = evt.to;
                                const newStatus = toColumn.dataset.status;
                                
                                const orderedIds = Array.from(toColumn.querySelectorAll('.sortable-card'))
                                    .map(card => card.dataset.taskId);

                                @this.call('updateTaskStatus', taskId, newStatus, orderedIds);
                            }
                        });
                    });
                }
            }
        }
    </script>
</div>
