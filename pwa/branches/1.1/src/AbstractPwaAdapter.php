<?php

declare(strict_types=1);

namespace Pollen\Pwa;

abstract class AbstractPwaAdapter implements PwaAdapterInterface
{
    use PwaProxy;

    /**
     * @param PwaInterface $pwa
     */
    public function __construct(PwaInterface $pwa)
    {
        $this->setPwa($pwa);
    }
}
