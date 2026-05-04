<?php
namespace app\Service;

use app\Model\ConversationAnswer;
use app\Repository\ConversationAnswerRepository;

class ConversationAnswerService
{
    private ConversationAnswerRepository $repository;

    public function __construct(ConversationAnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveAnswer(
        int $conversation_id,
        int $question_id,
        ?string $value_text = null,
        ?int $value_int = null,
        ?float $value_decimal = null,
        ?int $answer_id = null
    ): ConversationAnswer {

        // Validate at least one value is provided
        if ($value_text === null && $value_int === null && $value_decimal === null && $answer_id === null) {
            throw new \InvalidArgumentException('At least one value must be provided');
        }

        $answer = new ConversationAnswer(
            conversation_id: $conversation_id,
            question_id:     $question_id,
            answer_id:       $answer_id,
            value_text:      $value_text,
            value_int:       $value_int,
            value_decimal:   $value_decimal
        );

        return $this->repository->create($answer);
    }

    public function getAnswersByConversationId(int $conversation_id): array
    {
        if ($conversation_id <= 0) {
            throw new \InvalidArgumentException('Invalid conversation_id');
        }

        return $this->repository->findByConversationId($conversation_id);
    }
}