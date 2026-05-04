<?php
namespace app\Controller;

use app\Service\UserService;

class UserController
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    // POST /api/user
    public function create(): void
    {
        $data = $this->getJsonBody();

        $required = ['username', 'email', 'family_status'];
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
            $user = $this->service->createUser(
                username:       $data['username'],
                email:          $data['email'],
                family_status:  $data['family_status'],
                number_covered: $data['number_covered'] ?? 1,
                address:        $data['address'] ?? null,
                age:            $data['age'] ?? null,
                phone_number:   $data['phone_number'] ?? null
            );

            $this->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $this->format($user)
            ], 201);

        } catch (\InvalidArgumentException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // GET /api/user/{id}
    public function getById(int $id): void
    {
        $user = $this->service->getUserById($id);

        if (!$user) {
            $this->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
            return;
        }

        $this->json([
            'success' => true,
            'data'    => $this->format($user)
        ]);
    }

    // GET /api/user
    public function getAll(): void
    {
        $users = $this->service->getAllUsers();

        $this->json([
            'success' => true,
            'data'    => array_map(fn($u) => $this->format($u), $users)
        ]);
    }

    // DELETE /api/user/{id}
    public function delete(int $id): void
    {
        $deleted = $this->service->deleteUser($id);

        if (!$deleted) {
            $this->json([
                'success' => false,
                'message' => 'User not found or already deleted'
            ], 404);
            return;
        }

        $this->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    private function format($user): array
    {
        return [
            'user_id'        => $user->user_id,
            'username'       => $user->username,
            'email'          => $user->email,
            'address'        => $user->address,
            'age'            => $user->age,
            'phone_number'   => $user->phone_number,
            'number_covered' => $user->number_covered,
            'family_status'  => $user->family_status,
            'is_enabled'     => $user->is_enabled,
            'created_at'     => $user->created_at,
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