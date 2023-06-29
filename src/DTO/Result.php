<?php

namespace CptnHook\DTO;

use Throwable;

class Result
{
    protected function __construct(
        protected ResultStatus $status,
        protected string $group,
        protected string $name,
        protected ?int $duration,
        protected ?Throwable $error = null,
    ) {}

    public function getName(): string
    {
        return $this->group . '/' . $this->name;
    }

    public function failed(): bool
    {
        return $this->status == ResultStatus::FAILURE;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }

    public function getDurationMs(): ?int
    {
        return $this->duration;
    }

    public static function new(ResultStatus $status, string $group, string $name, ?int $duration, ?Throwable $e = null)
    {
        return new static($status, $group, $name, $duration, $e);
    }
}