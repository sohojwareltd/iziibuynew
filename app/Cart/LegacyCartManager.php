<?php

namespace App\Cart;

final class LegacyCartManager
{
    public function session(?string $instance = null): LegacyCartInstance
    {
        return new LegacyCartInstance($instance);
    }

    public function getTotalQuantity(): int
    {
        $total = 0;
        foreach (session()->all() as $key => $value) {
            if (! str_starts_with((string) $key, 'cart.')) {
                continue;
            }
            if (! is_array($value) || ! isset($value['items'])) {
                continue;
            }
            foreach ($value['items'] as $item) {
                $total += (int) ($item['quantity'] ?? 0);
            }
        }

        return $total;
    }

    /**
     * Clear all session carts, or a single shop instance (e.g. username slug).
     */
    public function clear(?string $instance = null): void
    {
        if ($instance !== null && $instance !== '') {
            session()->forget('cart.'.$instance);

            return;
        }

        foreach (array_keys(session()->all()) as $key) {
            if (str_starts_with((string) $key, 'cart.')) {
                session()->forget($key);
            }
        }
    }
}
