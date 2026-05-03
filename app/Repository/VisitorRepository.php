<?php
namespace app\Repository;

use app\Model\Visitor;
use PDO;

class VisitorRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Visitor $visitor): Visitor
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_visitor 
                (visitor_token, user_id, ip_address, terminal)
            VALUES 
                (:visitor_token, :user_id, :ip_address, :terminal)
        ");

        $stmt->execute([
            ':visitor_token' => $visitor->visitor_token,
            ':user_id'       => $visitor->user_id,
            ':ip_address'    => $visitor->ip_address,
            ':terminal'      => $visitor->terminal,
        ]);

        $visitor->visitor_id = (int) $this->pdo->lastInsertId();
        return $visitor;
    }

    public function findById(int $id): ?Visitor
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_visitor WHERE visitor_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findByToken(string $token): ?Visitor
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_visitor WHERE visitor_token = :token
        ");
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM tb_visitor");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    public function update(Visitor $visitor): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_visitor SET
                visitor_token = :visitor_token,
                user_id       = :user_id,
                ip_address    = :ip_address,
                terminal      = :terminal
            WHERE visitor_id  = :visitor_id
        ");

        return $stmt->execute([
            ':visitor_token' => $visitor->visitor_token,
            ':user_id'       => $visitor->user_id,
            ':ip_address'    => $visitor->ip_address,
            ':terminal'      => $visitor->terminal,
            ':visitor_id'    => $visitor->visitor_id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM tb_visitor WHERE visitor_id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    private function mapToModel(array $row): Visitor
    {
        return new Visitor(
            visitor_token: $row['visitor_token'],
            terminal:      $row['terminal'],
            user_id:       $row['user_id'],
            ip_address:    $row['ip_address'],
            visitor_id:    $row['visitor_id'],
            created_at:    $row['created_at'],
            updated_at:    $row['updated_at']
        );
    }
}