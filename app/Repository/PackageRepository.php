<?php
namespace app\Repository;

use app\Model\Package;
use PDO;

class PackageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Package $package): Package
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_package
                (package_name, min_budget, max_budget, min_age, max_age, max_number_covered, description, is_enabled)
            VALUES
                (:package_name, :min_budget, :max_budget, :min_age, :max_age, :max_number_covered, :description, :is_enabled)
        ");

        $stmt->execute([
            ':package_name'       => $package->package_name,
            ':min_budget'         => $package->min_budget,
            ':max_budget'         => $package->max_budget,
            ':min_age'            => $package->min_age,
            ':max_age'            => $package->max_age,
            ':max_number_covered' => $package->max_number_covered,
            ':description'        => $package->description,
            ':is_enabled'         => $package->is_enabled,
        ]);

        $package->package_id = (int) $this->pdo->lastInsertId();
        return $package;
    }

    public function findById(int $id): ?Package
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tb_package WHERE package_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        return $this->mapToModel($row);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT * FROM tb_package 
            ORDER BY package_id ASC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    public function findAllEnabled(): array
    {
        $stmt = $this->pdo->query("
            SELECT * FROM tb_package 
            WHERE is_enabled = 1
            ORDER BY min_budget ASC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    public function update(Package $package): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_package SET
                package_name       = :package_name,
                min_budget         = :min_budget,
                max_budget         = :max_budget,
                min_age            = :min_age,
                max_age            = :max_age,
                max_number_covered = :max_number_covered,
                description        = :description,
                is_enabled         = :is_enabled
            WHERE package_id       = :package_id
        ");

        return $stmt->execute([
            ':package_name'       => $package->package_name,
            ':min_budget'         => $package->min_budget,
            ':max_budget'         => $package->max_budget,
            ':min_age'            => $package->min_age,
            ':max_age'            => $package->max_age,
            ':max_number_covered' => $package->max_number_covered,
            ':description'        => $package->description,
            ':is_enabled'         => $package->is_enabled,
            ':package_id'         => $package->package_id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM tb_package WHERE package_id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    private function mapToModel(array $row): Package
    {
        return new Package(
            package_name:       $row['package_name'],
            min_budget:         (float) $row['min_budget'],
            max_budget:         (float) $row['max_budget'],
            max_number_covered: (int) $row['max_number_covered'],
            min_age:            $row['min_age'],
            max_age:            $row['max_age'],
            description:        $row['description'],
            is_enabled:         (bool) $row['is_enabled'],
            package_id:         (int) $row['package_id'],
            created_at:         $row['created_at'],
            updated_at:         $row['updated_at']
        );
    }
}