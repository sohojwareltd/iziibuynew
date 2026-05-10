<?php

namespace App\Cart;

final class LegacyCartAddResult
{
    public function __construct(
        protected LegacyCartInstance $cart,
        protected string $rowId
    ) {}

    public function associate(string $modelClass): self
    {
        $this->cart->associateModelForRow($this->rowId, $modelClass);

        return $this;
    }
}
