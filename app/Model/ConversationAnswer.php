<?php
namespace App\Model;

class ConversationAnswer
{
    public ?int $id;
    public int $conversation_id;
    public int $question_id;
    public ?int $answer_id;
    public ?string $value_text;
    public ?int $value_int;
    public ?float $value_decimal;
    public bool $is_valid;
    public ?string $raw_input;
    public ?string $created_at;

    public function __construct(
        int $conversation_id,
        int $question_id,
        ?int $answer_id = null,
        ?string $value_text = null,
        ?int $value_int = null,
        ?float $value_decimal = null,
        bool $is_valid = true,
        ?string $raw_input = null,
        ?int $id = null,
        ?string $created_at = null
    ) {
        $this->id              = $id;
        $this->conversation_id = $conversation_id;
        $this->question_id     = $question_id;
        $this->answer_id       = $answer_id;
        $this->value_text      = $value_text;
        $this->value_int       = $value_int;
        $this->value_decimal   = $value_decimal;
        $this->is_valid        = $is_valid;
        $this->raw_input       = $raw_input;
        $this->created_at      = $created_at;
    }
}