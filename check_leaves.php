<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$leaves = DB::table('leave_requests')->get();
echo 'Leaves: ' . count($leaves) . "\n";
$logs = DB::table('approval_logs')->get();
echo 'Logs: ' . count($logs) . "\n";
