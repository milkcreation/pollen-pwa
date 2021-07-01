<?php

declare(strict_types=1);

namespace Pollen\Pwa\Adapters;

use Pollen\Pwa\AbstractPwaAdapter;
use RuntimeException;

class WpPwaAdapter extends AbstractPwaAdapter
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!function_exists('add_action')) {
            throw new RuntimeException('add_action function is missing.');
        }

        if (!$this->isBooted()) {
            add_action('init', function () {
                if (!function_exists('get_bloginfo')) {
                    throw new RuntimeException('get_bloginfo function is missing.');
                }

                $this->pwa()->manifest()
                    ->setDefault('name', get_bloginfo('name'))
                    ->setDefault('short_name', get_bloginfo('name'));
            });

            if ($this->pwa()->config('asset.autoloader', true) === true) {
                add_action(
                    'wp_head',
                    function () {
                        echo '<!-- PWA Manifest -->';
                        echo $this->pwa()->manifest()->metaRegister();
                        echo '<!-- / PWA Manifest -->';
                    },
                    1
                );

                add_action(
                    'wp_head',
                    function () {
                        echo '<!-- PWA MetaTags -->';
                        echo $this->pwa()->manifest()->metaAppleTouchIcon();
                        echo $this->pwa()->manifest()->metaThemeColor();
                        echo '<!-- / PWA MetaTags -->';
                    },
                    5
                );

                add_action(
                    'wp_head',
                    function () {
                        echo '<!-- PWA Global Vars -->';
                        echo $this->pwa()->getGlobalVarsScripts();
                        echo '<!-- / PWA Global Vars -->';
                    },
                    25
                );

                add_action(
                    'wp_footer',
                    function () {
                        echo '<!-- PWA Service Worker Registration -->';
                        echo $this->pwa()->serviceWorker()->getRegisterScripts();
                        echo '<!-- / PWA Service Worker Registration -->';
                    }
                );
            }

            $this->setBooted();
        }
    }
}
