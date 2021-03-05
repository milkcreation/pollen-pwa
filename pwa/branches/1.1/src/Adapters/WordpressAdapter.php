<?php

declare(strict_types=1);

namespace Pollen\Pwa\Adapters;

use Pollen\Http\UrlHelper;
use Pollen\Pwa\AbstractPwaAdapter;
use Pollen\Pwa\PwaInterface;

class WordpressAdapter extends AbstractPwaAdapter
{
    /**
     * @param PwaInterface $pwa
     */
    public function __construct(PwaInterface $pwa)
    {
        parent::__construct($pwa);

        add_action(
            'wp_head',
            function () {
                echo $this->pwa()->getManifestScripts();
            },
            1
        );

        add_action(
            'wp_head',
            function () {
                echo $this->pwa()->getServiceWorkerScripts();
            }
        );
    }
}
