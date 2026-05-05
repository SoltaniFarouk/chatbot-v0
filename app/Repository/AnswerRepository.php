<?php
namespace App\Repository;

use App\Model\Answer;
use PDO;

class AnswerRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByQuestionId(int $question_id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_answer 
            WHERE question_id = :question_id
            ORDER BY answer_id ASC
        ");
        $stmt->execute([':question_id' => $question_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    private function mapToModel(array $row): Answer
    {
        return new Answer(
            question_id:  $row['question_id'],
            label:        $row['label'],
            answer_value: $row['answer_value'],
            answer_id:    $row['answer_id'],
            created_at:   $row['created_at'],
            updated_at:   $row['updated_at']
        );
    }
}