<?php
require 'vendor/autoload.php';
 
use App\Config\Env;
use App\Database\Connection;
use App\Repositories\MySQLBookRepository;
 
Env::load(__DIR__ . '/.env');
$pdo = Connection::get();
$repo = new MySQLBookRepository($pdo);
 
$books = $repo->findAll();
echo "Total buku: " . count($books) . PHP_EOL;
foreach ($books as $book) {
    echo "- {$book->getJudul()} oleh {$book->getPenulis()}" . PHP_EOL;
}
