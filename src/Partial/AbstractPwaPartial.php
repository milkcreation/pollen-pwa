<?php declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use Pollen\Pwa\Contracts\PwaManagerContract;
use Pollen\Pwa\PwaAwareTrait;
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Partial\PartialDriver;

class AbstractPwaPartial extends PartialDriver
{
    use PwaAwareTrait;

    /**
     * @param PwaManagerContract $pwaManager
     * @param PartialManager $partialManager
     */
    public function __construct(PwaManagerContract $pwaManager, PartialManager $partialManager)
    {
        $this->setPwaManager($pwaManager);

        parent::__construct($partialManager);
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pwa()->resources()->path('views/partial/' . $this->getAlias());
    }
}
