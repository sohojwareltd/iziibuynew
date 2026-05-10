<?php

namespace App\Services\Surfboard;

use App\Models\Shop;
use App\Payment\Surfboard\SurfboardPayment;
use PhpParser\Node\Expr\FuncCall;

class ShopOnboarding
{

    protected SurfboardPayment $surfboard;
    protected Shop $shop;

    protected $surfboardMerchant;
    protected $surfboardShop;
    protected $surfboardTerminal;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->surfboard = new SurfboardPayment();

        $this->initialize();
    }

    protected function createOrFindMerchant() {

    }
    protected function createOrFindShop() {

    }
    protected function createOrFindTerminal() {
        
    }


    protected function initialize() {
        $this->surfboardMerchant = $this->createOrFindMerchant();
        $this->surfboardMerchant = $this->createOrFindShop();
        $this->surfboardMerchant = $this->createOrFindShop();

    }
}
