<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoice = \App\Models\Invoice::has('items', '>=', 2)->first();
if (!$invoice) {
    $invoice = \App\Models\Invoice::factory()->has(\App\Models\InvoiceItem::factory()->count(3), 'items')->create();
}
echo $invoice->id;
