<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Support\Concerns\BootableTrait;

abstract class AbstractPwaAdapter implements PwaAdapterInterface
{
    use BootableTrait;
    use PwaProxy;

    /**
     * @param PwaInterface $pwa
     */
    public function __construct(PwaInterface $pwa)
    {
        $this->setPwa($pwa);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    abstract public function boot(): void;
}
