<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Opportunity;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\SupportTicket;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MainDashboard extends Component
{
    public $canViewPipeline = false;
    public $canViewInvoices = false;
    public $canViewTasks = false;
    public $canViewTickets = false;

    public $pipelineValue = 0;
    public $pipelineByStage = [];
    
    public $totalOutstanding = 0;
    public $totalOverdue = 0;
    public $dueWithin30Days = 0;

    public $openTasks = [];
    public $openTickets = [];

    public $recentActivity = [];

    public function mount()
    {
        $user = Auth::user();

        // 1. Check basic permissions
        $this->canViewPipeline = $user->hasPermissionTo('opportunities.view.all') || $user->hasPermissionTo('opportunities.view.own');
        $this->canViewInvoices = $user->hasPermissionTo('invoices.view.all') || $user->hasPermissionTo('invoices.view.own');
        $this->canViewTasks = $user->hasPermissionTo('tasks.view.all') || $user->hasPermissionTo('tasks.view.own');
        $this->canViewTickets = $user->hasPermissionTo('tickets.view.all') || $user->hasPermissionTo('tickets.view.own');

        // 2. Load Pipeline Value
        if ($this->canViewPipeline) {
            $query = Opportunity::query();
            if (!$user->hasPermissionTo('opportunities.view.all') && $user->employee) {
                $query->where('assigned_to', $user->employee->id);
            }
            
            // Only count non-won and non-lost stages? Or count all open pipeline. Let's exclude "won" and "lost".
            $query->whereHas('stage', function($q) {
                $q->where('is_won', false);
            });

            $this->pipelineValue = $query->sum('value');
            $this->pipelineByStage = $query->with('stage')->get()->groupBy('stage.name')->map(function ($items) {
                return $items->sum('value');
            })->toArray();
        }

        // 3. Load Invoice Aging
        if ($this->canViewInvoices) {
            $query = Invoice::query()->whereIn('status', ['sent', 'partially_paid', 'overdue']);
            if (!$user->hasPermissionTo('invoices.view.all') && $user->employee) {
                // Assuming invoices don't have assigned_to. Maybe restrict by project manager? 
                // Wait, invoices.view.own might not have a direct relation. Let's just do all or none for invoices for now, or assume view.own is empty.
                // In earlier modules we did not define invoice ownership. So if they only have view.own, they see 0.
                $query->where('id', 0); // Hide for now if they don't have .all
            }

            $invoices = $query->get();
            $this->totalOutstanding = $invoices->sum(fn($i) => $i->total - $i->paid_amount);
            $this->totalOverdue = $invoices->where('status', 'overdue')->sum(fn($i) => $i->total - $i->paid_amount);
            $this->dueWithin30Days = $invoices->filter(fn($i) => $i->due_date && Carbon::parse($i->due_date)->isBetween(now(), now()->addDays(30)))->sum(fn($i) => $i->total - $i->paid_amount);
        }

        // 4. Load Tasks and Tickets
        if ($this->canViewTasks && $user->employee) {
            $query = Task::where('status', '!=', 'completed');
            if (!$user->hasPermissionTo('tasks.view.all')) {
                $query->where('assigned_to', $user->employee->id);
            }
            $this->openTasks = $query->with('project')->latest()->take(5)->get();
        }

        if ($this->canViewTickets && $user->employee) {
            $query = SupportTicket::whereNotIn('status', ['resolved', 'closed']);
            if (!$user->hasPermissionTo('tickets.view.all')) {
                $query->where('assigned_to', $user->employee->id);
            }
            $this->openTickets = $query->latest()->take(5)->get();
        }

        // 5. Load Recent Activity
        // Limit to modules the user can see
        $allowedModules = [];
        if ($this->canViewPipeline) $allowedModules[] = 'Opportunities';
        if ($this->canViewInvoices) $allowedModules[] = 'Invoices';
        if ($this->canViewTickets) $allowedModules[] = 'SupportTickets';
        if ($user->hasPermissionTo('proposals.view.all') || $user->hasPermissionTo('proposals.view.own')) $allowedModules[] = 'Proposals';
        if ($user->hasPermissionTo('contracts.view.all') || $user->hasPermissionTo('contracts.view.own')) $allowedModules[] = 'Contracts';
        
        $this->recentActivity = AuditLog::with(['user'])
            ->whereIn('module', $allowedModules)
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.main-dashboard');
    }
}
