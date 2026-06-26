<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE leave_requests ADD is_cancellation BIT NOT NULL DEFAULT 0');
    echo "Added is_cancellation to leave_requests.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
