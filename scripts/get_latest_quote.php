<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
if (! file_exists($autoload)) {
    echo "vendor/autoload.php not found\n";
    exit(1);
}

require $autoload;

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$id = DB::table('quotes')->max('id');
if ($id) {
    echo $id.PHP_EOL;
} else {
    echo "none".PHP_EOL;
}
