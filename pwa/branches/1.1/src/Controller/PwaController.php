<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Routing\BaseController;
use Pollen\Http\ResponseInterface;
use Pollen\Pwa\PwaInterface;
use Pollen\Pwa\PwaProxy;
use Psr\Container\ContainerInterface as Container;
use Throwable;

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
     * Pwa Icon.
     *
     * @param string $icon
     *
     * @return ResponseInterface
     */
    public function icon(string $icon): ResponseInterface
    {
        return $this->cachedResponse(
            $this->file($this->pwa()->resources("/assets/dist/img/icons/$icon"), null, 'inline')
        );
    }

    /**
     * Manifest.
     *
     * @return ResponseInterface
     */
    public function manifest(): ResponseInterface
    {
        $content = $this->pwa()->manifest()->json();

        return $this->cachedResponse($this->response($content, 200, ['Content-Type' => 'application/manifest+json']));
    }

    /**
     * Pwa register.
     *
     * @return ResponseInterface
     */
    public function register(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/sw-register.js'));

        return $this->response($content, 200, ['Content-Type' => 'text/javascript']);
    }

    /**
     * Service Worker.
     *
     * @return ResponseInterface
     */
    public function serviceWorker(): ResponseInterface
    {
        $serviceWorker = $this->pwa()->serviceWorker();

        $vars = $this->pwa()->getGlobalVars();

        try {
            $vars = json_encode($vars, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            $vars = '{}';
        }
        $jsVars = "const PWA=$vars";

        $swScripts = file_get_contents($this->pwa()->resources('/assets/dist/js/sw.js'));

        return $this->response(
            $jsVars . $swScripts . implode(PHP_EOL, $serviceWorker->getAppendedScripts()),
            200,
            ['Content-Type' => 'text/javascript']
        );
    }
}
