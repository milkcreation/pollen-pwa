<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Routing\BaseController;
use Pollen\Http\JsonResponseInterface;
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
     * @return JsonResponseInterface
     */
    public function manifest(): JsonResponseInterface
    {
        return $this->json($this->pwa()->config('manifest', []));
    }

    /**
     * Service Worker
     *
     * @return ResponseInterface
     */
    public function serviceWorker(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/service-worker/sw.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }
}
