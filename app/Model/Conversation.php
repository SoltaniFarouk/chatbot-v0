<?php
namespace app\Model;

class Conversation
{
    public ?int $conversation_id;
    public int $visitor_id;
    public ?int $package_id;
    public string $status;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        int $visitor_id,
        string $status = 'active',
        ?int $package_id = null,
        ?int $conversation_id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->conversation_id = $conversation_id;
        $this->visitor_id      = $visitor_id;
        $this->package_id      = $package_id;
        $this->status          = $status;
        $this->created_at      = $created_at;
        $this->updated_at      = $updated_at;
    }
}