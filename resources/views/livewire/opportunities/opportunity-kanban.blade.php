<div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <div class="flex space-x-4 overflow-x-auto pb-4" 
         x-data="kanbanBoard()" 
         x-init="initBoard()">
         
        @foreach($stageData as $data)
            <div class="flex-shrink-0 w-80 bg-gray-100 dark:bg-gray-800 rounded-md shadow p-4 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase">{{ $data['stage']->name }}</h3>
                    <span class="text-xs text-gray-500 font-medium">
                        ${{ number_format($data['total_forecast'], 2) }}
                    </span>
                </div>
                
                <div class="flex-1 min-h-[500px] overflow-y-auto sortable-column" 
                     data-stage-id="{{ $data['stage']->id }}"
                     wire:ignore>
                    @foreach($data['opportunities'] as $opportunity)
                        <div class="bg-white dark:bg-gray-700 p-3 rounded shadow-sm mb-3 cursor-move border border-gray-200 dark:border-gray-600 sortable-card"
                             data-id="{{ $opportunity->id }}">
                            <div class="font-medium text-gray-900 dark:text-white mb-1">{{ $opportunity->title }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">{{ $opportunity->client?->name ?? 'No Client' }}</div>
                            <div class="mt-2 flex justify-between items-center text-xs text-gray-500">
                                <span>${{ number_format($opportunity->value, 2) }}</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">{{ $opportunity->probability }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function kanbanBoard() {
            return {
                initBoard() {
                    const columns = document.querySelectorAll('.sortable-column');
                    columns.forEach(col => {
                        new Sortable(col, {
                            group: 'kanban',
                            animation: 150,
                            ghostClass: 'opacity-50',
                            onEnd: (evt) => {
                                const itemEl = evt.item;
                                const opportunityId = itemEl.getAttribute('data-id');
                                const toColumn = evt.to;
                                const newStageId = toColumn.getAttribute('data-stage-id');
                                
                                const orderedIds = Array.from(toColumn.querySelectorAll('.sortable-card'))
                                    .map(el => el.getAttribute('data-id'))
                                    .filter(id => id !== null);
                                
                                @this.updateOpportunityStage(opportunityId, newStageId, orderedIds);
                            }
                        });
                    });
                }
            }
        }
    </script>
</div>
