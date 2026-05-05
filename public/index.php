<?php
use app\Connection\Database;

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

// ============ ROUTES ============

// Visitor
if ($method === 'POST' && $uri === '/api/visitor/register') {
    $visitorController->register();
}
elseif ($method === 'GET' && preg_match('#^/api/visitor/([a-f0-9]+)$#', $uri, $matches)) {
    $visitorController->getByToken($matches[1]);
}
elseif ($method === 'DELETE' && preg_match('#^/api/visitor/(\d+)$#', $uri, $matches)) {
    $visitorController->delete((int) $matches[1]);
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