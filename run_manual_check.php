<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Proposal;
use Livewire\Livewire;
use App\Livewire\Dashboard\MainDashboard;
use Illuminate\Support\Facades\Auth;

$salesUser = User::where('email', 'sales@example.com')->first();
$managerUser = User::where('email', 'manager@example.com')->first();

// 1. Pipeline value for Sales Exec
Auth::login($salesUser);
$htmlSales = Livewire::test(MainDashboard::class)->html();
preg_match('/<div class="text-3xl font-bold text-slate-800 mr-2">\$([0-9,.]+)<\/div>/', $htmlSales, $matches1);
echo "\n[MANUAL CHECK] Sales Exec Pipeline Value: $" . ($matches1[1] ?? 'NOT FOUND') . "\n";

// 2. Pipeline value for Manager
Auth::login($managerUser);
$htmlManager = Livewire::test(MainDashboard::class)->html();
preg_match('/<div class="text-3xl font-bold text-slate-800 mr-2">\$([0-9,.]+)<\/div>/', $htmlManager, $matches2);
echo "[MANUAL CHECK] Manager Pipeline Value: $" . ($matches2[1] ?? 'NOT FOUND') . "\n";

// 3. Send Proposal
$proposal = Proposal::where('status', 'draft')->first();
echo "[MANUAL CHECK] Proposal ID: " . $proposal->id . "\n";

// We can't easily simulate POST without Laravel's test client, so we will use the actual Action!
$action = app(\App\Actions\Proposals\SendProposalAction::class);
$action->execute($proposal);

$proposal->refresh();
echo "[MANUAL CHECK] Proposal status after send: " . $proposal->status . "\n";

// Check if log contains the email
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    if (strpos($logContent, 'ProposalSent') !== false || strpos($logContent, 'Message-ID:') !== false || strpos($logContent, 'To: ') !== false) {
        echo "[MANUAL CHECK] Email successfully logged in laravel.log\n";
    } else {
        echo "[MANUAL CHECK] Email NOT FOUND in laravel.log\n";
    }
}
