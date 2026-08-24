<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Welcome banner -->
        <div class="relative bg-indigo-200 p-4 sm:p-6 rounded-sm overflow-hidden mb-8">
            <div class="relative">
                @php
                    $hour = now()->format('H');
                    if ($hour < 12) {
                        $greeting = 'Good morning';
                    } elseif ($hour < 17) {
                        $greeting = 'Good afternoon';
                    } elseif ($hour < 21) {
                        $greeting = 'Good evening';
                    } else {
                        $greeting = 'Good night';
                    }
                @endphp
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-1">{{ $greeting }}, {{ Auth::user()->name }}. 👋</h1>
                <p>Here is what's happening with your projects today:</p>
            </div>
        </div>

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h2 class="text-xl md:text-2xl text-slate-800 font-bold">Overview</h2>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">

            <!-- Pipeline Value Widget -->
            @if($canViewPipeline)
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <div class="px-5 pt-5">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold text-slate-800">Pipeline Forecast</h2>
                    </header>
                    <div class="text-xs font-semibold text-slate-400 uppercase mb-1">Open Opportunities</div>
                    <div class="flex items-start">
                        <div class="text-3xl font-bold text-slate-800 mr-2">${{ number_format($pipelineValue, 2) }}</div>
                    </div>
                </div>
                <div class="grow">
                    <ul class="px-5 py-3 border-t border-slate-200 mt-4">
                        @foreach($pipelineByStage as $stage => $value)
                        <li class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-slate-800">{{ $stage }}</span>
                            <span class="text-sm font-medium text-slate-600">${{ number_format($value, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Invoice Aging Widget -->
            @if($canViewInvoices)
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <div class="px-5 pt-5">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold text-slate-800">Invoice Aging</h2>
                    </header>
                    <div class="text-xs font-semibold text-slate-400 uppercase mb-1">Total Outstanding</div>
                    <div class="flex items-start">
                        <div class="text-3xl font-bold text-slate-800 mr-2">${{ number_format($totalOutstanding, 2) }}</div>
                    </div>
                </div>
                <div class="grow">
                    <ul class="px-5 py-3 border-t border-slate-200 mt-4">
                        <li class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-red-600">Overdue</span>
                            <span class="text-sm font-bold text-red-600">${{ number_format($totalOverdue, 2) }}</span>
                        </li>
                        <li class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-amber-600">Due within 30 Days</span>
                            <span class="text-sm font-bold text-amber-600">${{ number_format($dueWithin30Days, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Activity / Tasks Widget -->
            @if($canViewTasks || $canViewTickets)
            <div class="flex flex-col col-span-full xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">My Action Items</h2>
                </header>
                <div class="p-3">
                    @if($canViewTasks && count($openTasks) > 0)
                    <div class="text-xs font-semibold text-slate-400 uppercase mb-2">Open Tasks</div>
                    <ul class="my-1">
                        @foreach($openTasks as $task)
                        <li class="flex px-2 py-1">
                            <div class="w-9 h-9 rounded-full shrink-0 bg-indigo-500 my-2 mr-3 flex items-center justify-center">
                                <svg class="w-4 h-4 fill-current text-indigo-50" viewBox="0 0 16 16">
                                    <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                </svg>
                            </div>
                            <div class="grow flex items-center border-b border-slate-100 text-sm py-2">
                                <div class="grow flex justify-between">
                                    <div class="self-center"><a class="font-medium text-slate-800 hover:text-slate-900" href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></div>
                                    <div class="shrink-0 self-start ml-2">
                                        <span class="font-medium text-slate-800">{{ $task->due_date ? Carbon\Carbon::parse($task->due_date)->diffForHumans() : 'No due date' }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    @if($canViewTickets && count($openTickets) > 0)
                    <div class="text-xs font-semibold text-slate-400 uppercase mt-4 mb-2">Open Tickets</div>
                    <ul class="my-1">
                        @foreach($openTickets as $ticket)
                        <li class="flex px-2 py-1">
                            <div class="w-9 h-9 rounded-full shrink-0 bg-rose-500 my-2 mr-3 flex items-center justify-center">
                                <svg class="w-4 h-4 fill-current text-rose-50" viewBox="0 0 16 16">
                                    <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3 11L8 9 5 11 6 8 4 6h3l1-3 1 3h3l-2 2 1 3z" />
                                </svg>
                            </div>
                            <div class="grow flex items-center border-b border-slate-100 text-sm py-2">
                                <div class="grow flex justify-between">
                                    <div class="self-center"><a class="font-medium text-slate-800 hover:text-slate-900" href="{{ route('tickets.show', $ticket) }}">{{ $ticket->subject }}</a></div>
                                    <div class="shrink-0 self-start ml-2">
                                        <span class="font-medium text-slate-800">{{ ucfirst($ticket->priority) }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    
                    @if((!$canViewTasks || count($openTasks) == 0) && (!$canViewTickets || count($openTickets) == 0))
                    <div class="text-sm text-slate-500 p-2 text-center">No open action items. Great job!</div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recent Activity Feed -->
            <div class="col-span-full bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Recent Activity</h2>
                </header>
                <div class="p-3">
                    @if(count($recentActivity) > 0)
                    <div>
                        <ul class="my-1">
                            @foreach($recentActivity as $log)
                            <li class="flex px-2 py-2 border-b border-slate-100 last:border-0">
                                <div class="grow flex items-center text-sm py-2">
                                    <div class="grow flex justify-between">
                                        <div class="self-center">
                                            <span class="font-medium text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                                            <span class="text-slate-500">{{ str_replace('_', ' ', $log->action) }}</span> a
                                            <span class="font-medium text-slate-800">{{ rtrim($log->module, 's') }}</span>.
                                        </div>
                                        <div class="shrink-0 self-start ml-2">
                                            <span class="text-xs text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="text-sm text-slate-500 p-2 text-center">No recent activity.</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
