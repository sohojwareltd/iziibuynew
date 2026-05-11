<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;

test('iziibuy import legacy cms command is registered', function () {
    $commands = app(Kernel::class)->all();

    expect($commands)->toHaveKey('iziibuy:import-legacy-cms');
});
