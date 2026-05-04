<?php
namespace app\Service;

use app\Model\Conversation;
use app\Repository\ConversationRepository;

class ConversationService
{
    private ConversationRepository $repository;

    public function __construct(ConversationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function startConversation(int $visitor_id): Conversation
    {
        $existing = $this->repository->findActiveByVisitorId($visitor_id);
        if ($existing) return $existing;

        $conversation = new Conversation(
            visitor_id: $visitor_id,
            status:     'active'
        );

        return $this->repository->create($conversation);
    }

    public function getConversationById(int $id): ?Conversation
    {
        return $this->repository->findById($id);
    }
}