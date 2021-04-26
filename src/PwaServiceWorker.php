<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\Response;
use Pollen\Http\ResponseInterface;
use Pollen\Http\UrlHelper;
use Throwable;

class PwaServiceWorker implements PwaServiceWorkerInterface
{
    use PwaProxy;

    /**
     * Liste des scripts ajoutés au Service Worker.
     * @var array
     */
    protected $appendScripts = [];

    /**
     * @var string
     */
    protected $serviceWorkerPath;

    /**
     * @var string
     */
    protected $serviceWorkerRegisterPath;

    /**
     * @param PwaInterface|null $pwa
     */
    public function __construct(?PwaInterface $pwa = null)
    {
        if ($pwa !== null) {
            $this->setPwa($pwa);
        }
    }

    /**
     * @inheritDoc
     */
    public function appendScripts(string $scripts): PwaServiceWorkerInterface
    {
        $this->appendScripts[] = $scripts;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRegisterScripts(): string
    {
        $urlHelper = new UrlHelper();
        $src = $urlHelper->getAbsoluteUrl($this->pwa()->resources('/assets/dist/js/sw-register.js'));

        return "<script type=\"text/javascript\" src=\"$src\"></script>";
    }

    /**
     * @inheritDoc
     */
    public function response(): ResponseInterface
    {
        $vars = $this->pwa()->getGlobalVars();

        try {
            $vars = json_encode($vars, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            $vars = '{}';
        }
        $jsVars = "const PWA=$vars";

        $swScripts = file_get_contents($this->pwa()->resources('/assets/dist/js/sw.js'));

        return new Response(
            $jsVars . $swScripts . implode(PHP_EOL, $this->appendScripts), 200, ['Content-Type' => 'application/javascript']
        );
    }
}