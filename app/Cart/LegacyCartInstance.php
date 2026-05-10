<?php

namespace App\Cart;

use Illuminate\Support\Collection;

/**
 * Session-backed cart compatible with a subset of darryldecode/cart used in this app.
 */
final class LegacyCartInstance
{
    protected string $sessionKey;

    public function __construct(?string $instance = null)
    {
        $this->sessionKey = 'cart.'.($instance ?? 'default');
    }

    protected function raw(): array
    {
        return session()->get($this->sessionKey, ['items' => []]);
    }

    protected function persist(array $data): void
    {
        session()->put($this->sessionKey, $data);
    }

    /**
     * @param  array<string, mixed>|int|string  $idOrPayload
     */
    public function add(mixed $idOrPayload, ?string $name = null, ?float $price = null, int $quantity = 1, array $options = []): LegacyCartAddResult
    {
        $data = $this->raw();
        if (is_array($idOrPayload)) {
            $payload = $idOrPayload;
            $id = $payload['id'];
            $name = (string) $payload['name'];
            $price = (float) $payload['price'];
            $quantity = (int) $payload['quantity'];
            $options = (array) ($payload['attributes'] ?? []);
        } else {
            $id = $idOrPayload;
        }

        $rowId = (string) $id;
        if (isset($data['items'][$rowId])) {
            $data['items'][$rowId]['quantity'] = (int) $data['items'][$rowId]['quantity'] + $quantity;
        } else {
            $data['items'][$rowId] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'options' => $options,
                'model' => null,
                'model_class' => null,
            ];
        }
        $this->persist($data);

        return new LegacyCartAddResult($this, $rowId);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(string $rowId, array $attributes): void
    {
        $data = $this->raw();
        if (! isset($data['items'][$rowId])) {
            return;
        }

        if (isset($attributes['quantity']) && is_array($attributes['quantity'])) {
            $qty = $attributes['quantity']['value'] ?? 1;
            $attributes['quantity'] = (int) $qty;
        }

        $data['items'][$rowId] = array_merge($data['items'][$rowId], $attributes);
        $this->persist($data);
    }

    public function remove(string $rowId): void
    {
        $data = $this->raw();
        unset($data['items'][$rowId]);
        $this->persist($data);
    }

    public function clear(): void
    {
        $this->persist(['items' => []]);
    }

    public function isEmpty(): bool
    {
        return $this->getContent()->isEmpty();
    }

    public function getTotalQuantity(): int
    {
        return (int) $this->getContent()->sum(function (LegacyCartItem $item): int {
            return (int) ($item->quantity ?? 0);
        });
    }

    /**
     * @return Collection<int, LegacyCartItem>
     */
    public function getContent(): Collection
    {
        $items = [];
        foreach ($this->raw()['items'] ?? [] as $rowId => $row) {
            if (! is_array($row)) {
                continue;
            }
            $modelClass = $row['model_class'] ?? null;
            if (is_string($modelClass) && class_exists($modelClass) && empty($row['model'])) {
                $row['model'] = $modelClass::find($row['id'] ?? null);
            }
            $items[$rowId] = new LegacyCartItem($row);
        }

        return collect($items);
    }

    public function getSubTotal(): float
    {
        return (float) $this->getContent()->sum(function (LegacyCartItem $item) {
            $price = is_numeric($item->price) ? (float) $item->price : 0.0;
            $qty = (int) $item->quantity;

            return $price * $qty;
        });
    }

    public function getTotal(): float
    {
        return $this->getSubTotal();
    }

    public function associateModelForRow(string $rowId, string $modelClass): void
    {
        $data = $this->raw();
        if (! isset($data['items'][$rowId])) {
            return;
        }
        $model = $modelClass::find($data['items'][$rowId]['id'] ?? null);
        if ($model) {
            $data['items'][$rowId]['model'] = $model;
            $data['items'][$rowId]['model_class'] = $modelClass;
        }
        $this->persist($data);
    }
}
