<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Routing\BaseController;
use Pollen\Pwa\PwaInterface;
use Pollen\Pwa\PwaProxyTrait;
use Psr\Container\ContainerInterface as Container;

abstract class AbstractController extends BaseController
{
    use PwaProxyTrait;

    /**
     * @param PwaInterface $pwa
     * @param Container|null $container
     */
    public function __construct(PwaInterface $pwa, ?Container $container = null)
    {
        $this->setPwa($pwa);

        parent::__construct($container);
    }

    /**
     * @inheritDoc
     */
    public function viewEngineDirectory(): string
    {
        return $this->pwa()->resources('/views');
    }
}
