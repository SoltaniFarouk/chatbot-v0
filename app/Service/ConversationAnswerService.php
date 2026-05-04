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
        ?int $answer_id = null,
        bool $is_valid = true,
        ?string $raw_input = null
    ): ConversationAnswer {

        $answer = new ConversationAnswer(
            conversation_id: $conversation_id,
            question_id: $question_id,
            answer_id: $answer_id,
            value_text: $value_text,
            value_int: $value_int,
            value_decimal: $value_decimal,
            is_valid: $is_valid,
            raw_input: $raw_input
        );

        return $this->repository->create($answer);
    }

    public function getAnswersByConversationId(int $conversation_id): array
    {
        return $this->repository->findByConversationId($conversation_id);
    }
}
