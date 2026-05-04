<?php
namespace App\Controller;

use App\Service\QuestionService;

class QuestionController
{
    private QuestionService $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
    }

    // GET /api/question/{id}
    public function getById(int $id): void
    {
        try {
            $question = $this->service->getQuestionById($id);

            $this->json([
                'success' => true,
                'data'    => [
                    'question_id' => $question->question_id,
                    'description' => $question->description,
                    'step_order'  => $question->step_order,
                    'is_active'   => $question->is_active,
                    'created_at'  => $question->created_at,
                ]
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}