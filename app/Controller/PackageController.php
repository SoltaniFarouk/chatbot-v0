<?php
namespace app\Controller;

use app\Model\Package;
use app\Service\PackageService;

class PackageController
{
    private PackageService $service;

    public function __construct(PackageService $service)
    {
        $this->service = $service;
    }

    // POST /api/package
    public function create(): void
    {
        $data = $this->getJsonBody();

        $required = ['package_name', 'min_budget', 'max_budget'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json([
                    'success' => false,
                    'message' => "Field '$field' is required"
                ], 400);
                return;
            }
        }

        try {
            $package = $this->service->createPackage(
                package_name:       $data['package_name'],
                min_budget:         (float) $data['min_budget'],
                max_budget:         (float) $data['max_budget'],
                max_number_covered: (int) ($data['max_number_covered'] ?? 1),
                min_age:            isset($data['min_age']) ? (int) $data['min_age'] : null,
                max_age:            isset($data['max_age']) ? (int) $data['max_age'] : null,
                description:        $data['description'] ?? null,
                is_enabled:         (bool) ($data['is_enabled'] ?? true)
            );

            $this->json([
                'success' => true,
                'message' => 'Package created successfully',
                'data'    => $this->format($package)
            ], 201);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/package
    public function getAll(): void
    {
        try {
            $packages = $this->service->getAllPackages();

            $this->json([
                'success' => true,
                'data'    => array_map(fn($p) => $this->format($p), $packages)
            ]);

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/package/enabled
    public function getAllEnabled(): void
    {
        try {
            $packages = $this->service->getAllEnabledPackages();

            $this->json([
                'success' => true,
                'data'    => array_map(fn($p) => $this->format($p), $packages)
            ]);

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/package/{id}
    public function getById(int $id): void
    {
        try {
            $package = $this->service->getPackageById($id);

            $this->json([
                'success' => true,
                'data'    => $this->format($package)
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // PUT /api/package/{id}
    public function update(int $id): void
    {
        $data = $this->getJsonBody();

        $required = ['package_name', 'min_budget', 'max_budget'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json([
                    'success' => false,
                    'message' => "Field '$field' is required"
                ], 400);
                return;
            }
        }

        try {
            $package = new Package(
                package_name:       $data['package_name'],
                min_budget:         (float) $data['min_budget'],
                max_budget:         (float) $data['max_budget'],
                max_number_covered: (int) ($data['max_number_covered'] ?? 1),
                min_age:            isset($data['min_age']) ? (int) $data['min_age'] : null,
                max_age:            isset($data['max_age']) ? (int) $data['max_age'] : null,
                description:        $data['description'] ?? null,
                is_enabled:         (bool) ($data['is_enabled'] ?? true),
                package_id:         $id
            );

            $updated = $this->service->updatePackage($package);

            if (!$updated) {
                $this->json([
                    'success' => false,
                    'message' => 'Package not found or nothing updated'
                ], 404);
                return;
            }

            $this->json([
                'success' => true,
                'message' => 'Package updated successfully',
                'data'    => $this->format($package)
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/package/{id}
    public function delete(int $id): void
    {
        try {
            $deleted = $this->service->deletePackage($id);

            if (!$deleted) {
                $this->json([
                    'success' => false,
                    'message' => 'Package not found or already deleted'
                ], 404);
                return;
            }

            $this->json([
                'success' => true,
                'message' => 'Package deleted successfully'
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function format(Package $package): array
    {
        return [
            'package_id'         => $package->package_id,
            'package_name'       => $package->package_name,
            'min_budget'         => $package->min_budget,
            'max_budget'         => $package->max_budget,
            'min_age'            => $package->min_age,
            'max_age'            => $package->max_age,
            'max_number_covered' => $package->max_number_covered,
            'description'        => $package->description,
            'is_enabled'         => $package->is_enabled,
            'created_at'         => $package->created_at,
        ];
    }

    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}