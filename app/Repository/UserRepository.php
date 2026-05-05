<?php

namespace app\Repository;

use app\Model\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(User $user): User
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_user 
                (username, email, address, age, phone_number, number_covered, family_status, is_enabled)
            VALUES 
                (:username, :email, :address, :age, :phone_number, :number_covered, :family_status, :is_enabled)
        ");

        $stmt->execute([
            ':username'       => $user->username,
            ':email'          => $user->email,
            ':address'        => $user->address,
            ':age'            => $user->age,
            ':phone_number'   => $user->phone_number,
            ':number_covered' => $user->number_covered,
            ':family_status'  => $user->family_status,
            ':is_enabled'     => $user->is_enabled,
        ]);

        $user->user_id = (int) $this->pdo->lastInsertId();
        return $user;
    }

    public function fast_create(User $user): User
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO tb_user 
            (username, email)
        VALUES 
            (:username, :email)
        ");

        $stmt->execute([
            ':username' => $user->username,
            ':email'    => $user->email,
        ]);

        $user->user_id = (int) $this->pdo->lastInsertId();
        return $user;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_user WHERE user_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_user WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_user WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM tb_user");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_user SET
                username       = :username,
                email          = :email,
                address        = :address,
                age            = :age,
                phone_number   = :phone_number,
                number_covered = :number_covered,
                family_status  = :family_status,
                is_enabled     = :is_enabled
            WHERE user_id      = :user_id
        ");

        return $stmt->execute([
            ':username'       => $user->username,
            ':email'          => $user->email,
            ':address'        => $user->address,
            ':age'            => $user->age,
            ':phone_number'   => $user->phone_number,
            ':number_covered' => $user->number_covered,
            ':family_status'  => $user->family_status,
            ':is_enabled'     => $user->is_enabled,
            ':user_id'        => $user->user_id,
        ]);
    }

    public function updateById(int $id, array $data): bool
    {
        if (empty($data)) {
            return false; // nothing to update
        }

        // Build dynamic SET clause
        $fields = [];
        $params = [':user_id' => $id];

        foreach ($data as $column => $value) {
            $fields[] = "$column = :$column";
            $params[":$column"] = $value;
        }

        $sql = "UPDATE tb_user SET " . implode(', ', $fields) . " WHERE user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM tb_user WHERE user_id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    private function mapToModel(array $row): User
    {
        return new User(
            username: $row['username'],
            email: $row['email'],
            family_status: $row['family_status'],
            number_covered: $row['number_covered'],
            address: $row['address'],
            age: $row['age'],
            phone_number: $row['phone_number'],
            is_enabled: (bool) $row['is_enabled'],
            user_id: $row['user_id'],
            created_at: $row['created_at'],
            updated_at: $row['updated_at']
        );
    }
}
