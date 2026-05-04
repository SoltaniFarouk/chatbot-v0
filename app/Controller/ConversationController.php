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

    // POST /api/conversation/answer
    public function saveAnswer(): void
    {
        $data = $this->getJsonBody();

        if (empty($data['conversation_id']) || empty($data['question_id'])) {
            $this->json([
                'success' => false,
                'message' => 'conversation_id and question_id are required'
            ], 400);
            return;
        }

        try {
            $answer = $this->service->saveAnswer(
                conversation_id: (int) $data['conversation_id'],
                question_id:     (int) $data['question_id'],
                value_text:      $data['value_text'] ?? null,
                value_int:       isset($data['value_int']) ? (int) $data['value_int'] : null,
                value_decimal:   isset($data['value_decimal']) ? (float) $data['value_decimal'] : null,
                answer_id:       isset($data['answer_id']) ? (int) $data['answer_id'] : null
            );

            $this->json([
                'success' => true,
                'message' => 'Answer saved',
                'data'    => [
                    'id'              => $answer->id,
                    'conversation_id' => $answer->conversation_id,
                    'question_id'     => $answer->question_id,
                    'value_text'      => $answer->value_text,
                    'value_int'       => $answer->value_int,
                    'value_decimal'   => $answer->value_decimal,
                    'created_at'      => $answer->created_at,
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