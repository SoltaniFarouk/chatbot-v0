<?php
namespace app\Repository;

use app\Model\Question;
use PDO;

class QuestionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Question
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_question 
            WHERE question_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    private function mapToModel(array $row): Question
    {
        return new Question(
            description: $row['description'],
            step_order:  $row['step_order'],
            is_active:   (bool) $row['is_active'],
            question_id: $row['question_id'],
            created_at:  $row['created_at'],
            updated_at:  $row['updated_at']
        );
    }
}