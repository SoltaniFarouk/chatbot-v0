<?php

use app\Connection\Database;
use app\Repository\VisitorRepository;
use app\Service\VisitorService;
use app\Controller\VisitorController;

use app\Repository\UserRepository;
use app\Service\UserService;
use app\Controller\UserController;

// Load .env
$envPath = '/var/www/.env';
if (file_exists($envPath)) {
    foreach (file($envPath) as $line) {
        $line = trim($line);
        if ($line && strpos($line, '=') !== false) {
            putenv($line);
        }
    }
}

// Autoload classes
spl_autoload_register(function ($class) {
    $path = '/var/www/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) require_once $path;
});

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// Bootstrap DB + Controllers
$pdo        = (new Database())->connect();
$repository = new VisitorRepository($pdo);
$service    = new VisitorService($repository);
$controller = new VisitorController($service);

$userRepository = new UserRepository($pdo);
$userService    = new UserService($userRepository);
$userController = new UserController($userService);
// ============ ROUTES ============

// POST /api/visitor/register
if ($method === 'POST' && $uri === '/api/visitor/register') {
    $controller->register();
}

// GET /api/visitor/{token}
elseif ($method === 'GET' && preg_match('#^/api/visitor/([a-f0-9]+)$#', $uri, $matches)) {
    $controller->getByToken($matches[1]);
}

// DELETE /api/visitor/{id}
elseif ($method === 'DELETE' && preg_match('#^/api/visitor/(\d+)$#', $uri, $matches)) {
    $controller->delete((int) $matches[1]);
}

// GET / → serve chatbot UI
elseif ($method === 'GET' && $uri === '') {
    require_once __DIR__ . '/home.php';
}

// 404
else {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Route not found']);
}