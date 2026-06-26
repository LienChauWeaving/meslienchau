<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE users ADD delegate_id BIGINT NULL');
    echo "Added delegate_id to users.\n";
} catch (\Exception $e) {
    echo "Error users: " . $e->getMessage() . "\n";
}

try {
    DB::statement('ALTER TABLE approval_logs ADD actual_approver_id BIGINT NULL');
    echo "Added actual_approver_id to approval_logs.\n";
} catch (\Exception $e) {
    echo "Error approval_logs: " . $e->getMessage() . "\n";
}
