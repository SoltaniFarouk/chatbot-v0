<?php
namespace app\Model;

class Answer
{
    public ?int $answer_id;
    public int $question_id;
    public string $label;
    public ?string $answer_value;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        int $question_id,
        string $label,
        ?string $answer_value = null,
        ?int $answer_id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->answer_id    = $answer_id;
        $this->question_id  = $question_id;
        $this->label        = $label;
        $this->answer_value = $answer_value;
        $this->created_at   = $created_at;
        $this->updated_at   = $updated_at;
    }
}