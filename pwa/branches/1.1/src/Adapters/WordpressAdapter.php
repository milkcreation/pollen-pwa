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
                $urlHelper = new UrlHelper();

                echo "<link rel=\"manifest\" href=\"" . $urlHelper->getRelativePath('/manifest.webmanifest') . "\">";
            },
            1
        );

        add_action(
            'wp_head',
            function () {
                $urlHelper = new UrlHelper();
                $src = $urlHelper->getAbsoluteUrl(
                    $this->pwa()->resources('/assets/dist/js/service-worker/sw-register.js')
                );

                echo "<script type=\"text/javascript\" src=\"" . $src . "\">";
            }
        );

        add_action(
            'wp_footer',
            function () {
                echo $this->pwa()->partial('pwa-install-promotion');
            },
            1
        );
    }
}
