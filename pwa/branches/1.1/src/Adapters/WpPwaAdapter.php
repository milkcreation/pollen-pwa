<?php

declare(strict_types=1);

namespace Pollen\Pwa\Adapters;

use Pollen\Pwa\AbstractPwaAdapter;

class WpPwaAdapter extends AbstractPwaAdapter
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            add_action(
                'wp_head',
                function () {
                    if ($this->pwa()->config('wordpress.autoload', true) === true) {
                        echo $this->pwa()->getManifestScripts();
                    }
                },
                1
            );

            add_action(
                'wp_head',
                function () {
                    if ($this->pwa()->config('wordpress.autoload', true) === true) {
                        echo $this->pwa()->getServiceWorkerScripts();
                    }
                }
            );

            $this->setBooted();
        }
    }
}
