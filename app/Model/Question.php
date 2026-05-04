<?php
namespace app\Model;

class Question
{
    public ?int $question_id;
    public string $description;
    public int $step_order;
    public bool $is_active;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        string $description,
        int $step_order,
        bool $is_active = true,
        ?int $question_id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->question_id  = $question_id;
        $this->description  = $description;
        $this->step_order   = $step_order;
        $this->is_active    = $is_active;
        $this->created_at   = $created_at;
        $this->updated_at   = $updated_at;
    }
}