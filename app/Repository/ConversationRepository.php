<?php
namespace app\Repository;

use app\Model\Conversation;
use PDO;

class ConversationRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Conversation $conversation): Conversation
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_conversation 
                (visitor_id, package_id, status)
            VALUES 
                (:visitor_id, :package_id, :status)
        ");

        $stmt->execute([
            ':visitor_id' => $conversation->visitor_id,
            ':package_id' => $conversation->package_id,
            ':status'     => $conversation->status,
        ]);

        $conversation->conversation_id = (int) $this->pdo->lastInsertId();
        return $conversation;
    }

    public function findById(int $id): ?Conversation
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_conversation WHERE conversation_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findActiveByVisitorId(int $visitor_id): ?Conversation
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_conversation 
            WHERE visitor_id = :visitor_id 
            AND status = 'active'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([':visitor_id' => $visitor_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    private function mapToModel(array $row): Conversation
    {
        return new Conversation(
            visitor_id:      $row['visitor_id'],
            status:          $row['status'],
            package_id:      $row['package_id'],
            conversation_id: $row['conversation_id'],
            created_at:      $row['created_at'],
            updated_at:      $row['updated_at']
        );
    }
}