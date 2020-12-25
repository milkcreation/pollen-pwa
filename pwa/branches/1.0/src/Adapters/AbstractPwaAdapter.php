<?php declare(strict_types=1);

namespace Pollen\Pwa\Adapters;

use Pollen\Pwa\PwaAwareTrait;
use Pollen\Pwa\Contracts\PwaAdapterContract;
use Pollen\Pwa\Contracts\PwaManagerContract;

abstract class AbstractPwaAdapter implements PwaAdapterContract
{
    use PwaAwareTrait;

    /**
     * @param PwaManagerContract $pwaManager
     */
    public function __construct(PwaManagerContract $pwaManager)
    {
        $this->setPwaManager($pwaManager);
    }
}
