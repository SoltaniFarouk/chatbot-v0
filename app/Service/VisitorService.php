<?php
namespace app\Service;

use app\Model\Visitor;
use app\Repository\VisitorRepository;

class VisitorService
{
    private VisitorRepository $repository;

    private const ALLOWED_TERMINALS = [
        'mobile', 'desktop', 'tablet', 'bot', 'unknown'
    ];

    public function __construct(VisitorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function registerVisitor(
        string $ip_address,
        string $terminal = 'unknown',
        ?int $user_id = null
    ): Visitor {

        // Validate terminal
        if (!in_array($terminal, self::ALLOWED_TERMINALS)) {
            $terminal = 'unknown';
        }

        // Generate unique token
        $token = $this->generateToken();

        // Make sure token is unique
        while ($this->repository->findByToken($token) !== null) {
            $token = $this->generateToken();
        }

        $visitor = new Visitor(
            visitor_token: $token,
            terminal:      $terminal,
            user_id:       $user_id,
            ip_address:    $ip_address
        );

        return $this->repository->create($visitor);
    }

    public function getVisitorByToken(string $token): ?Visitor
    {
        return $this->repository->findByToken($token);
    }

    public function getVisitorById(int $id): ?Visitor
    {
        return $this->repository->findById($id);
    }

    public function getAllVisitors(): array
    {
        return $this->repository->findAll();
    }

    public function deleteVisitor(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}