<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \DB::table('quote_items')->where('quote_id', 18)->get();
foreach ($rows as $r) {
    echo json_encode((array) $r).PHP_EOL;
}
