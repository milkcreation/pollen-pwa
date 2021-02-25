<?php

declare(strict_types=1);

namespace Pollen\Pwa\Adapters;

use Pollen\Pwa\Contracts\PwaManagerContract;
use tiFy\Support\Proxy\Url;

class WordpressAdapter extends AbstractPwaAdapter
{
    /**
     * @param PwaManagerContract $pwaManager
     */
    public function __construct(PwaManagerContract $pwaManager)
    {
        parent::__construct($pwaManager);

        add_action(
            'wp_head',
            function () {
                echo "<link rel=\"manifest\" href=\"" . Url::root('/manifest.webmanifest')->path() . "\">";
            },
            1
        );

        add_action(
            'wp_footer',
            function () {
                echo partial('pwa-install-promotion');
            },
            1
        );
    }
}
