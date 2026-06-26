<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('users', function(Blueprint $table) {
    if (!Schema::hasColumn('users', 'department_name')) {
        $table->string('department_name')->nullable();
    }
    if (!Schema::hasColumn('users', 'job_title')) {
        $table->string('job_title')->nullable();
    }
});
echo "Done\n";
