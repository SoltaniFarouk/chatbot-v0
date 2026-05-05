<?php

namespace app\Controller;

use app\Service\VisitorService;

class VisitorController
{
    private VisitorService $service;

    public function __construct(VisitorService $service)
    {
        $this->service = $service;
    }

    // POST /api/visitor/register
    public function register(): void
    {
        $data     = $this->getJsonBody();
        $terminal = $data['terminal'] ?? 'unknown';
        $user_id  = $data['user_id'] ?? null;
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $visitor = $this->service->registerVisitor(
            ip_address: $ip,
            terminal: $terminal,
            user_id: $user_id
        );

        $this->json([
            'success' => true,
            'message' => 'Visitor registered successfully',
            'data'    => [
                'visitor_id'    => $visitor->visitor_id,
                'visitor_token' => $visitor->visitor_token,
                'terminal'      => $visitor->terminal,
                'ip_address'    => $visitor->ip_address,
                'created_at'    => $visitor->created_at,
            ]
        ], 201);
    }

    // GET /api/visitor/{token}
    public function getByToken(string $token): void
    {
        $visitor = $this->service->getVisitorByToken($token);

        if (!$visitor) {
            $this->json([
                'success' => false,
                'message' => 'Visitor not found'
            ], 404);
            return;
        }

        $this->json([
            'success' => true,
            'data'    => [
                'visitor_id'    => $visitor->visitor_id,
                'visitor_token' => $visitor->visitor_token,
                'terminal'      => $visitor->terminal,
                'ip_address'    => $visitor->ip_address,
                'user_id'       => $visitor->user_id,
                'created_at'    => $visitor->created_at,
            ]
        ]);
    }

    // DELETE /api/visitor/{id}
    public function delete(int $id): void
    {
        $deleted = $this->service->deleteVisitor($id);

        if (!$deleted) {
            $this->json([
                'success' => false,
                'message' => 'Visitor not found or already deleted'
            ], 404);
            return;
        }

        $this->json([
            'success' => true,
            'message' => 'Visitor deleted successfully'
        ]);
    }

    // PUT /api/visitor/token/{token}
    public function updateByToken(string $token): void
    {
        $data = $this->getJsonBody();

        if (empty($data)) {
            $this->json([
                'success' => false,
                'message' => 'No data provided'
            ], 400);
            return;
        }

        try {
            $updated = $this->service->updateByToken($token, $data);

            if (!$updated) {
                $this->json([
                    'success' => false,
                    'message' => 'Visitor not found or nothing updated'
                ], 404);
                return;
            }

            $this->json([
                'success' => true,
                'message' => 'Visitor updated successfully'
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

    // PUT /api/visitor/{id}
    public function updateById(int $id): void
    {
        $data = $this->getJsonBody();

        if (empty($data)) {
            $this->json([
                'success' => false,
                'message' => 'No data provided'
            ], 400);
            return;
        }

        try {
            $updated = $this->service->updateById($id, $data);

            if (!$updated) {
                $this->json([
                    'success' => false,
                    'message' => 'Visitor not found or nothing updated'
                ], 404);
                return;
            }

            $this->json([
                'success' => true,
                'message' => 'Visitor updated successfully'
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
