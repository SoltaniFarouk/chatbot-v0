<?php
namespace app\Model;

class Package
{
    public ?int $package_id;
    public string $package_name;
    public float $min_budget;
    public float $max_budget;
    public ?int $min_age;
    public ?int $max_age;
    public int $max_number_covered;
    public ?string $description;
    public bool $is_enabled;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        string $package_name,
        float $min_budget,
        float $max_budget,
        int $max_number_covered = 1,
        ?int $min_age = null,
        ?int $max_age = null,
        ?string $description = null,
        bool $is_enabled = true,
        ?int $package_id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->package_id         = $package_id;
        $this->package_name       = $package_name;
        $this->min_budget         = $min_budget;
        $this->max_budget         = $max_budget;
        $this->min_age            = $min_age;
        $this->max_age            = $max_age;
        $this->max_number_covered = $max_number_covered;
        $this->description        = $description;
        $this->is_enabled         = $is_enabled;
        $this->created_at         = $created_at;
        $this->updated_at         = $updated_at;
    }
}