<?php
namespace app\Service;

use app\Model\Package;
use app\Repository\PackageRepository;

class PackageService
{
    private PackageRepository $repository;

    public function __construct(PackageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createPackage(
        string $package_name,
        float $min_budget,
        float $max_budget,
        int $max_number_covered = 1,
        ?int $min_age = null,
        ?int $max_age = null,
        ?string $description = null,
        bool $is_enabled = true
    ): Package {

        $this->validate($package_name, $min_budget, $max_budget, $max_number_covered);

        $package = new Package(
            package_name:       $package_name,
            min_budget:         $min_budget,
            max_budget:         $max_budget,
            max_number_covered: $max_number_covered,
            min_age:            $min_age,
            max_age:            $max_age,
            description:        $description,
            is_enabled:         $is_enabled
        );

        return $this->repository->create($package);
    }

    public function getPackageById(int $id): ?Package
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid package_id');
        }

        $package = $this->repository->findById($id);

        if (!$package) {
            throw new \InvalidArgumentException('Package not found');
        }

        return $package;
    }

    public function getAllPackages(): array
    {
        return $this->repository->findAll();
    }

    public function getAllEnabledPackages(): array
    {
        return $this->repository->findAllEnabled();
    }

    public function updatePackage(Package $package): bool
    {
        if (!$package->package_id) {
            throw new \InvalidArgumentException('Package ID is required for update');
        }

        $this->validate(
            $package->package_name,
            $package->min_budget,
            $package->max_budget,
            $package->max_number_covered
        );

        return $this->repository->update($package);
    }

    public function deletePackage(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid package_id');
        }

        return $this->repository->delete($id);
    }

    private function validate(
        string $package_name,
        float $min_budget,
        float $max_budget,
        int $max_number_covered
    ): void {
        if (empty($package_name)) {
            throw new \InvalidArgumentException('Package name is required');
        }
        if ($min_budget <= 0) {
            throw new \InvalidArgumentException('Min budget must be greater than 0');
        }
        if ($max_budget <= 0) {
            throw new \InvalidArgumentException('Max budget must be greater than 0');
        }
        if ($min_budget >= $max_budget) {
            throw new \InvalidArgumentException('Min budget must be less than max budget');
        }
        if ($max_number_covered < 1) {
            throw new \InvalidArgumentException('Max number covered must be at least 1');
        }
    }
}