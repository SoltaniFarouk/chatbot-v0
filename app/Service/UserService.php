<?php

namespace app\Service;

use app\Model\User;
use app\Repository\UserRepository;

class UserService
{
    private UserRepository $repository;

    private const ALLOWED_FAMILY_STATUS = [
        'single',
        'married',
        'divorced',
        'widowed'
    ];

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUser(
        string $username,
        string $email,
        string $family_status,
        int $number_covered = 1,
        ?string $address = null,
        ?int $age = null,
        ?string $phone_number = null
    ): User {

        // Validate family status
        if (!in_array($family_status, self::ALLOWED_FAMILY_STATUS)) {
            throw new \InvalidArgumentException('Invalid family status');
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        // Check username already exists
        if ($this->repository->findByUsername($username)) {
            throw new \InvalidArgumentException('Username already exists');
        }

        // Check email already exists
        if ($this->repository->findByEmail($email)) {
            throw new \InvalidArgumentException('Email already exists');
        }

        $user = new User(
            username: $username,
            email: $email,
            family_status: $family_status,
            number_covered: $number_covered,
            address: $address,
            age: $age,
            phone_number: $phone_number
        );

        return $this->repository->create($user);
    }

    // Fast create with minimal info (for chatbot)
    // Auto-generates username if not provided, and sets defaults for other fields
    public function fastCreateUser(?string $username, string $email): User
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }

        // Auto-generate username if not provided
        if (empty($username)) {
            $username = explode('@', $email)[0];
        }

        // Ensure uniqueness (important!)
        $baseUsername = $username;
        $i = 1;

        while ($this->repository->findByUsername($username)) {
            $username = $baseUsername . $i; // john → john1 → john2
            $i++;
        }

        // Check email uniqueness
        if ($this->repository->findByEmail($email)) {
            throw new \InvalidArgumentException("Email already exists");
        }

        $user = new User(
            username: $username,
            email: $email,
            family_status: 'not_defined',
            number_covered: 1,
            address: null,
            age: null,
            phone_number: null,
            is_enabled: true
        );

        return $this->repository->fast_create($user);
    }

    public function getUserById(int $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    public function getAllUsers(): array
    {
        return $this->repository->findAll();
    }

    public function updateUser(User $user): bool
    {
        if (!in_array($user->family_status, self::ALLOWED_FAMILY_STATUS)) {
            throw new \InvalidArgumentException('Invalid family status');
        }

        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        return $this->repository->update($user);
    }

    // function updateuserbyid using updateById in repository
    public function updateUserById(int $id, array $data): bool
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('No data provided for update');
        }

        // Allowed fields (security)
        $allowedFields = [
            'username',
            'email',
            'address',
            'age',
            'phone_number',
            'number_covered',
            'family_status',
            'is_enabled'
        ];

        // Filter only allowed fields
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($filteredData)) {
            throw new \InvalidArgumentException('No valid fields to update');
        }

        // Validation
        if (
            isset($filteredData['email']) &&
            !filter_var($filteredData['email'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        if (
            isset($filteredData['family_status']) &&
            !in_array($filteredData['family_status'], self::ALLOWED_FAMILY_STATUS)
        ) {
            throw new \InvalidArgumentException('Invalid family status');
        }

        // Check username uniqueness
        if (isset($filteredData['username'])) {
            $existing = $this->repository->findByUsername($filteredData['username']);
            if ($existing && $existing->user_id !== $id) {
                throw new \InvalidArgumentException('Username already exists');
            }
        }

        // Check email uniqueness
        if (isset($filteredData['email'])) {
            $existing = $this->repository->findByEmail($filteredData['email']);
            if ($existing && $existing->user_id !== $id) {
                throw new \InvalidArgumentException('Email already exists');
            }
        }

        return $this->repository->updateById($id, $filteredData);
    }

    public function deleteUser(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
