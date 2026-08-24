<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoiceId = $argv[1];
$invoice = \App\Models\Invoice::find($invoiceId);
$items = $invoice->items()->orderBy('sort_order')->get();
$order = [];
foreach ($items as $item) {
    $order[] = $item->id;
}
echo implode(',', $order);
