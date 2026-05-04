<?php
namespace app\Service;

use app\Model\Conversation;
use app\Model\ConversationAnswer;
use app\Repository\ConversationRepository;
use app\Repository\ConversationAnswerRepository;

class ConversationService
{
    private ConversationRepository $conversationRepository;
    private ConversationAnswerRepository $answerRepository;

    public function __construct(
        ConversationRepository $conversationRepository,
        ConversationAnswerRepository $answerRepository
    ) {
        $this->conversationRepository = $conversationRepository;
        $this->answerRepository       = $answerRepository;
    }

    public function startConversation(int $visitor_id): Conversation
    {
        // Check if active conversation already exists
        $existing = $this->conversationRepository->findActiveByVisitorId($visitor_id);
        if ($existing) {
            return $existing;
        }

        $conversation = new Conversation(
            visitor_id: $visitor_id,
            status:     'active'
        );

        return $this->conversationRepository->create($conversation);
    }

    public function saveAnswer(
        int $conversation_id,
        int $question_id,
        ?string $value_text = null,
        ?int $value_int = null,
        ?float $value_decimal = null,
        ?int $answer_id = null
    ): ConversationAnswer {

        $answer = new ConversationAnswer(
            conversation_id: $conversation_id,
            question_id:     $question_id,
            answer_id:       $answer_id,
            value_text:      $value_text,
            value_int:       $value_int,
            value_decimal:   $value_decimal
        );

        return $this->answerRepository->create($answer);
    }

    public function getAnswersByConversationId(int $conversation_id): array
    {
        return $this->answerRepository->findByConversationId($conversation_id);
    }
}