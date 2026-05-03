<?php
namespace app\Model;

class Visitor
{
    public ?int $visitor_id;
    public string $visitor_token;
    public ?int $user_id;
    public ?string $ip_address;
    public string $terminal;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        string $visitor_token,
        string $terminal = 'unknown',
        ?int $user_id = null,
        ?string $ip_address = null,
        ?int $visitor_id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->visitor_id    = $visitor_id;
        $this->visitor_token = $visitor_token;
        $this->user_id       = $user_id;
        $this->ip_address    = $ip_address;
        $this->terminal      = $terminal;
        $this->created_at    = $created_at;
        $this->updated_at    = $updated_at;
    }
}