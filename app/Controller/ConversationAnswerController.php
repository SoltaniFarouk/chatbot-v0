<?php
namespace App\Controller;

use App\Service\ConversationAnswerService;

class ConversationAnswerController
{
    private ConversationAnswerService $service;

    public function __construct(ConversationAnswerService $service)
    {
        $this->service = $service;
    }

    // POST /api/conversation-answer
    public function save(): void
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
                'message' => 'Answer saved successfully',
                'data'    => [
                    'id'              => $answer->id,
                    'conversation_id' => $answer->conversation_id,
                    'question_id'     => $answer->question_id,
                    'answer_id'       => $answer->answer_id,
                    'value_text'      => $answer->value_text,
                    'value_int'       => $answer->value_int,
                    'value_decimal'   => $answer->value_decimal,
                    'created_at'      => $answer->created_at,
                ]
            ], 201);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/conversation-answer/{conversation_id}
    public function getByConversationId(int $conversation_id): void
    {
        try {
            $answers = $this->service->getAnswersByConversationId($conversation_id);

            $this->json([
                'success' => true,
                'data'    => array_map(fn($a) => [
                    'id'              => $a->id,
                    'conversation_id' => $a->conversation_id,
                    'question_id'     => $a->question_id,
                    'answer_id'       => $a->answer_id,
                    'value_text'      => $a->value_text,
                    'value_int'       => $a->value_int,
                    'value_decimal'   => $a->value_decimal,
                    'created_at'      => $a->created_at,
                ], $answers)
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
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