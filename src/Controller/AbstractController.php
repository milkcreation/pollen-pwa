<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Pwa\Contracts\PwaManagerContract;
use Pollen\Pwa\PwaAwareTrait;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\View\Engine;
use tiFy\Routing\BaseController;
use tiFy\Support\Proxy\View;

abstract class AbstractController extends BaseController
{
    use PwaAwareTrait;

    /**
     * @param PwaManagerContract $pwaManager
     * @param Container|null $container
     */
    public function __construct(PwaManagerContract $pwaManager, ?Container $container = null)
    {
        $this->setPwaManager($pwaManager);

        parent::__construct($container);
    }

    /**
     * Moteur d'affichage des gabarits d'affichage.
     *
     * @return Engine
     */
    public function viewEngine(): Engine
    {
        return View::getPlatesEngine(
            [
                'directory' => $this->pwa()->resources('views'),
            ]
        );
    }
}
