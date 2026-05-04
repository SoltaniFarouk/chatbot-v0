<?php
namespace app\Repository;

use app\Model\ConversationAnswer;
use PDO;

class ConversationAnswerRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(ConversationAnswer $answer): ConversationAnswer
{
    $stmt = $this->pdo->prepare("
        INSERT INTO tb_conversation_answer
            (conversation_id, question_id, answer_id, value_text, value_int, value_decimal, is_valid, raw_input)
        VALUES
            (:conversation_id, :question_id, :answer_id, :value_text, :value_int, :value_decimal, :is_valid, :raw_input)
    ");

    $stmt->execute([
        ':conversation_id' => $answer->conversation_id,
        ':question_id'     => $answer->question_id,
        ':answer_id'       => $answer->answer_id,
        ':value_text'      => $answer->value_text,
        ':value_int'       => $answer->value_int,
        ':value_decimal'   => $answer->value_decimal,
        ':is_valid'        => $answer->is_valid,
        ':raw_input'       => $answer->raw_input,
    ]);

    $answer->id = (int) $this->pdo->lastInsertId();
    return $answer;
}

    public function findByConversationId(int $conversation_id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_conversation_answer 
            WHERE conversation_id = :conversation_id
            ORDER BY created_at ASC
        ");
        $stmt->execute([':conversation_id' => $conversation_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    private function mapToModel(array $row): ConversationAnswer
    {
        return new ConversationAnswer(
            conversation_id: $row['conversation_id'],
            question_id:     $row['question_id'],
            answer_id:       $row['answer_id'],
            value_text:      $row['value_text'],
            value_int:       $row['value_int'],
            value_decimal:   $row['value_decimal'],
            id:              $row['id'],
            created_at:      $row['created_at']
        );
    }
}