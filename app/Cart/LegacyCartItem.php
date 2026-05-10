<?php

namespace App\Cart;

final class LegacyCartItem
{
    public function __construct(
        public array $row
    ) {}

    public function __get(string $key): mixed
    {
        return $this->row[$key] ?? null;
    }

    public function __isset(string $key): bool
    {
        return array_key_exists($key, $this->row);
    }
}
