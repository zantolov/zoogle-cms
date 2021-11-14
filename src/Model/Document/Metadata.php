<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Model\Document;

final class Metadata implements DocumentElement
{
    /**
     * @var array<string, mixed>
     */
    private array $values;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->values);
    }

    public function toString(): string
    {
        return \Safe\json_encode($this->values);
    }
}
