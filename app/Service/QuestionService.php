<?php
namespace app\Service;

use app\Model\Question;
use app\Repository\QuestionRepository;

class QuestionService
{
    private QuestionRepository $repository;

    public function __construct(QuestionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getQuestionById(int $id): ?Question
    {
        $question = $this->repository->findById($id);

        if (!$question) {
            throw new \InvalidArgumentException('Question not found');
        }

        if (!$question->is_active) {
            throw new \InvalidArgumentException('Question is not active');
        }

        return $question;
    }
}