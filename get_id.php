<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = \App\Models\Proposal::where('status', 'draft')->first()->id;
echo "DRAFT_ID=" . $id . "\n";
