<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Asset\Assets\CdnAsset;
use Pollen\Asset\Assets\InlineAsset;
use Pollen\Event\TriggeredEvent;
use Pollen\Support\Proxy\EventProxy;

class PwaServiceWorker implements PwaServiceWorkerInterface
{
    use EventProxy;
    use PwaProxy;

    protected array $appendScripts = [];

    protected string $serviceWorkerPath = '';

    protected string $serviceWorkerRegisterPath = '';

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
    public function getAppendedScripts(): array
    {
        return $this->appendScripts;
    }

    /**
     * @inheritDoc
     */
    public function getRegisterScripts(): string
    {
        $src = $this->pwa()->getEndpointUrl('register');

        return "<script type=\"text/javascript\" src=\"$src\"></script>";
    }
}