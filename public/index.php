<?php
use app\Connection\Database;
use app\Router\Router;

use app\Repository\VisitorRepository;
use app\Service\VisitorService;
use app\Controller\VisitorController;

use app\Repository\UserRepository;
use app\Service\UserService;
use app\Controller\UserController;

use app\Repository\QuestionRepository;
use app\Service\QuestionService;
use app\Controller\QuestionController;

use app\Repository\ConversationRepository;
use app\Service\ConversationService;
use app\Controller\ConversationController;

use app\Repository\ConversationAnswerRepository;
use app\Service\ConversationAnswerService;
use app\Controller\ConversationAnswerController;

use app\Repository\AnswerRepository;
use app\Service\AnswerService;
use app\Controller\AnswerController;

use app\Repository\PackageRepository;
use app\Service\PackageService;
use app\Controller\PackageController;

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

// Autoload
spl_autoload_register(function ($class) {
    $path = '/var/www/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) require_once $path;
});

// Request info
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// Bootstrap
$pdo = (new Database())->connect();
$router = new Router();

// Visitor
$visitorController = new VisitorController(
    new VisitorService(
        new VisitorRepository($pdo)
    )
);

// User
$userController = new UserController(
    new UserService(
        new UserRepository($pdo)
    )
);

// Question
$questionController = new QuestionController(
    new QuestionService(
        new QuestionRepository($pdo)
    )
);

// Conversation
$conversationController = new ConversationController(
    new ConversationService(
        new ConversationRepository($pdo)
    )
);

// ConversationAnswer
$conversationAnswerController = new ConversationAnswerController(
    new ConversationAnswerService(
        new ConversationAnswerRepository($pdo)
    )
);
// Answer
$answerController = new AnswerController(
    new AnswerService(
        new AnswerRepository($pdo)
    )
);
// Package
$packageController = new PackageController(
    new PackageService(
        new PackageRepository($pdo)
    )
);
// ============ ROUTES ============
/*
// Visitor
// POST /api/visitor/register
if ($method === 'POST' && $uri === '/api/visitor/register') {
    $visitorController->register();
}
// GET /api/visitor/{token}
elseif ($method === 'GET' && preg_match('#^/api/visitor/([a-f0-9]+)$#', $uri, $matches)) {
    $visitorController->getByToken($matches[1]);
}
// DELETE /api/visitor/{id}
elseif ($method === 'DELETE' && preg_match('#^/api/visitor/(\d+)$#', $uri, $matches)) {
    $visitorController->delete((int) $matches[1]);
}
// PUT /api/visitor/token/{token}
elseif ($method === 'PUT' && preg_match('#^/api/visitor/token/([a-f0-9]+)$#', $uri, $matches)) {
    $visitorController->updateByToken($matches[1]);
}
// PUT /api/visitor/{id}
elseif ($method === 'PUT' && preg_match('#^/api/visitor/(\d+)$#', $uri, $matches)) {
    $visitorController->updateById((int) $matches[1]);
}


// User
elseif ($method === 'GET' && $uri === '/api/user') {
    $userController->getAll();
}
elseif ($method === 'POST' && $uri === '/api/user') {
    $userController->create();
}
elseif ($method === 'POST' && $uri === '/api/user/fast') {
    $userController->fastCreate();
}
elseif ($method === 'GET' && preg_match('#^/api/user/(\d+)$#', $uri, $matches)) {
    $userController->getById((int) $matches[1]);
}
elseif ($method === 'DELETE' && preg_match('#^/api/user/(\d+)$#', $uri, $matches)) {
    $userController->delete((int) $matches[1]);
}
if ($method === 'PUT' && preg_match('#^/api/user/(\d+)$#', $uri, $matches)) {
    $userController->updateById((int)$matches[1]);
    exit;
}

// Question
elseif ($method === 'GET' && $uri === '/api/question') {
    $questionController->getAll();
}
elseif ($method === 'GET' && preg_match('#^/api/question/(\d+)$#', $uri, $matches)) {
    $questionController->getById((int) $matches[1]);
}

// Conversation
elseif ($method === 'POST' && $uri === '/api/conversation') {
    $conversationController->create();
}

// ConversationAnswer
elseif ($method === 'POST' && $uri === '/api/conversation-answer') {
    $conversationAnswerController->save();
}
elseif ($method === 'GET' && preg_match('#^/api/conversation-answer/(\d+)$#', $uri, $matches)) {
    $conversationAnswerController->getByConversationId((int) $matches[1]);
}

// Answer
elseif ($method === 'GET' && preg_match('#^/api/answer/question/(\d+)$#', $uri, $matches)) {
    $answerController->getByQuestionId((int) $matches[1]);
}

// Package
// GET /api/package
elseif ($method === 'GET' && $uri === '/api/package') {
    $packageController->getAll();
}

// GET /api/package/enabled
elseif ($method === 'GET' && $uri === '/api/package/enabled') {
    $packageController->getAllEnabled();
}

// GET /api/package/{id}
elseif ($method === 'GET' && preg_match('#^/api/package/(\d+)$#', $uri, $matches)) {
    $packageController->getById((int) $matches[1]);
}

// POST /api/package
elseif ($method === 'POST' && $uri === '/api/package') {
    $packageController->create();
}

// PUT /api/package/{id}
elseif ($method === 'PUT' && preg_match('#^/api/package/(\d+)$#', $uri, $matches)) {
    $packageController->update((int) $matches[1]);
}

// DELETE /api/package/{id}
elseif ($method === 'DELETE' && preg_match('#^/api/package/(\d+)$#', $uri, $matches)) {
    $packageController->delete((int) $matches[1]);
}

// Home
elseif ($method === 'GET' && $uri === '') {
    require_once __DIR__ . '/home.php';
}

// 404
else {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Route not found']);
}
    */

// ============ ROUTER ============
$router = new Router();

// Visitor
$router->post('/api/visitor/register',          fn() => $visitorController->register());
$router->get('/api/visitor/{token}',            fn($token) => $visitorController->getByToken($token));
$router->put('/api/visitor/token/{token}',      fn($token) => $visitorController->updateByToken($token));
$router->put('/api/visitor/{id}',               fn($id) => $visitorController->updateById((int) $id));
$router->delete('/api/visitor/{id}',            fn($id) => $visitorController->delete((int) $id));

// User
$router->get('/api/user',                       fn() => $userController->getAll());
$router->post('/api/user',                      fn() => $userController->create());
$router->get('/api/user/{id}',                  fn($id) => $userController->getById((int) $id));
$router->put('/api/user/{id}',                  fn($id) => $userController->updateById((int) $id));
$router->delete('/api/user/{id}',               fn($id) => $userController->delete((int) $id));

// Question
$router->get('/api/question',                   fn() => $questionController->getAll());
$router->get('/api/question/{id}',              fn($id) => $questionController->getById((int) $id));

// Conversation
$router->post('/api/conversation',              fn() => $conversationController->create());

// ConversationAnswer
$router->post('/api/conversation-answer',       fn() => $conversationAnswerController->save());
$router->get('/api/conversation-answer/{id}',   fn($id) => $conversationAnswerController->getByConversationId((int) $id));

// Answer
$router->get('/api/answer/question/{id}',       fn($id) => $answerController->getByQuestionId((int) $id));

// Package
$router->get('/api/package',                    fn() => $packageController->getAll());
$router->get('/api/package/enabled',            fn() => $packageController->getAllEnabled());
$router->get('/api/package/{id}',               fn($id) => $packageController->getById((int) $id));
$router->post('/api/package',                   fn() => $packageController->create());
$router->put('/api/package/{id}',               fn($id) => $packageController->update((int) $id));
$router->delete('/api/package/{id}',            fn($id) => $packageController->delete((int) $id));

// Home
$router->get('', fn() => require_once __DIR__ . '/home.php');

// Dispatch
$router->dispatch($method, $uri);
