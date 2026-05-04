<?php
namespace app\Model;

class User
{
    public ?int $user_id;
    public string $username;
    public string $email;
    public ?string $address;
    public ?int $age;
    public ?string $phone_number;
    public int $number_covered;
    public string $family_status;
    public bool $is_enabled;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        string $username,
        string $email,
        string $family_status,
        int $number_covered = 1,
        ?string $address = null,
        ?int $age = null,
        ?string $phone_number = null,
        bool $is_enabled = true,
        ?int $user_id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->user_id        = $user_id;
        $this->username       = $username;
        $this->email          = $email;
        $this->address        = $address;
        $this->age            = $age;
        $this->phone_number   = $phone_number;
        $this->number_covered = $number_covered;
        $this->family_status  = $family_status;
        $this->is_enabled     = $is_enabled;
        $this->created_at     = $created_at;
        $this->updated_at     = $updated_at;
    }
}