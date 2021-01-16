<?php

declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use Pollen\Pwa\Contracts\PwaManagerContract;
use Pollen\Pwa\PwaAwareTrait;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\PartialDriver;

abstract class AbstractPwaPartial extends PartialDriver
{
    use PwaAwareTrait;

    /**
     * @param PwaManagerContract $pwaManager
     * @param PartialContract $partialManager
     */
    public function __construct(PwaManagerContract $pwaManager, PartialContract $partialManager)
    {
        $this->setPwaManager($pwaManager);

        parent::__construct($partialManager);
    }
}
