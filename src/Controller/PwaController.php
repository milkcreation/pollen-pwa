<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Routing\BaseController;
use Pollen\Http\ResponseInterface;
use Pollen\Pwa\PwaInterface;
use Pollen\Pwa\PwaProxy;
use Psr\Container\ContainerInterface as Container;

class PwaController extends BaseController
{
    use PwaProxy;

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
     * Manifest
     *
     * @return ResponseInterface
     */
    public function manifest(): ResponseInterface
    {
        return $this->pwa()->manifest()->response();
    }

    /**
     * Service Worker
     *
     * @return ResponseInterface
     */
    public function serviceWorker(): ResponseInterface
    {
        return $this->pwa()->serviceWorker()->response();
    }
}
