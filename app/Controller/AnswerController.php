<?php
namespace app\Controller;

use app\Service\AnswerService;

class AnswerController
{
    private AnswerService $service;

    public function __construct(AnswerService $service)
    {
        $this->service = $service;
    }

    // GET /api/answer/question/{question_id}
    public function getByQuestionId(int $question_id): void
    {
        try {
            $answers = $this->service->getAnswersByQuestionId($question_id);

            if (empty($answers)) {
                $this->json([
                    'success' => true,
                    'data'    => [],
                    'message' => 'No answers found for this question'
                ]);
                return;
            }

            $this->json([
                'success' => true,
                'data'    => array_map(fn($a) => [
                    'answer_id'    => $a->answer_id,
                    'question_id'  => $a->question_id,
                    'label'        => $a->label,
                    'answer_value' => $a->answer_value,
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

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
}