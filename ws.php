<?php

require_once 'vendor/autoload.php';
require_once 'Canvas.php';

use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
$pdo = new PDO('mysql:host=127.0.0.1;dbname=canvas', 'root', '');

$ws = new WsServer(new Canvas($pdo));

// Make sure you're running this as root
$server = IoServer::factory(new HttpServer($ws), 4000);
$server->run();