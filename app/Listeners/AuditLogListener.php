<?php

namespace App\Listeners;

use App\Events\AuditableAction;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

class AuditLogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AuditableAction $event): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $event->action,
            'module' => $event->module,
            'record_type' => get_class($event->model),
            'record_id' => $event->model->id,
            'old_values' => $event->oldValues,
            'new_values' => $event->newValues,
            'ip_address' => Request::ip(),
        ]);
    }
}
