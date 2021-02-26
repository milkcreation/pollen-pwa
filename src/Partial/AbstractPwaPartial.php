<?php

declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use Pollen\Pwa\PwaInterface;
use Pollen\Pwa\PwaProxyTrait;
use Pollen\Partial\PartialDriver;
use Pollen\Partial\PartialManagerInterface;

abstract class AbstractPwaPartial extends PartialDriver
{
    use PwaProxyTrait;

    /**
     * @param PwaInterface $pwa
     * @param PartialManagerInterface $partialManager
     */
    public function __construct(PwaInterface $pwa, PartialManagerInterface $partialManager)
    {
        $this->setPwa($pwa);

        parent::__construct($partialManager);
    }
}
