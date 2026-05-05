<?php
namespace App\Service;

use App\Repository\AnswerRepository;

class AnswerService
{
    private AnswerRepository $repository;

    public function __construct(AnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAnswersByQuestionId(int $question_id): array
    {
        if ($question_id <= 0) {
            throw new \InvalidArgumentException('Invalid question_id');
        }

        return $this->repository->findByQuestionId($question_id);
    }
}