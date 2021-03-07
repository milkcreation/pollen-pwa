<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Support\Concerns\BootableTraitInterface;

interface PwaAdapterInterface extends BootableTraitInterface, PwaProxyInterface
{
    /**
     * Chargement.
     *
     * @return void
     */
    public function boot(): void;
}