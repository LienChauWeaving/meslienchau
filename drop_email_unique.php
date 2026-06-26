<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('DROP INDEX users_email_unique ON users;');
    echo "Successfully dropped UNIQUE index on email column.\n";
} catch (\Exception $e) {
    try {
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_email_unique;');
        echo "Successfully dropped UNIQUE constraint on email column.\n";
    } catch (\Exception $e2) {
        echo "Error dropping index/constraint: " . $e2->getMessage() . "\n";
    }
}
