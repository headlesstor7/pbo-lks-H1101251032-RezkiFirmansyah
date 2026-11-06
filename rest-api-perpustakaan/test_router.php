<?php
require 'vendor/autoload.php';
use App\Core\Router;
use App\Core\Request;
 
// Simulasi URI
$_SERVER['REQUEST_URI'] = '/api/hello';
$_SERVER['REQUEST_METHOD'] = 'GET';
 
$router = new Router();
$router->get('/api/hello', function() {
    echo "✅ Router bekerja! Endpoint /api/hello terpanggil." . PHP_EOL;
});
 
$router->dispatch(new Request());
