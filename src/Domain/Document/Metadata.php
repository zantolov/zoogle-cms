<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Domain\Document;

class Metadata implements DocumentElement
{
    private array $values = [];

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function all(): array
    {
        return $this->values;
    }

    public function get(string $key)
    {
        return $this->values[$key] ?? null;
    }

    public function set(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function toString(): string
    {
        return json_encode($this->values);
    }
}
