<?php
namespace app\Controller;

use app\Service\ConversationService;

class ConversationController
{
    private ConversationService $service;

    public function __construct(ConversationService $service)
    {
        $this->service = $service;
    }

    // POST /api/conversation
    public function create(): void
    {
        $data = $this->getJsonBody();

        if (empty($data['visitor_id'])) {
            $this->json([
                'success' => false,
                'message' => 'visitor_id is required'
            ], 400);
            return;
        }

        try {
            $conversation = $this->service->startConversation(
                (int) $data['visitor_id']
            );

            $this->json([
                'success' => true,
                'message' => 'Conversation started',
                'data'    => [
                    'conversation_id' => $conversation->conversation_id,
                    'visitor_id'      => $conversation->visitor_id,
                    'status'          => $conversation->status,
                    'created_at'      => $conversation->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}