<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = app('router')->getRoutes();
echo "Total routes: " . $routes->count() . "\n";

foreach ($routes as $route) {
    $methods = $route->methods();
    $uri = $route->uri();
    $name = $route->getName() ?? 'unnamed';
    echo implode('|', $methods) . " " . $uri . " => " . $name . "\n";
}