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
            add_action('init', function () {
                $this->pwa()->manifest()
                    ->setDefault('name', get_bloginfo('name'))
                    ->setDefault('short_name', get_bloginfo('name'));
            });

            /**  */
            add_action(
                'wp_head',
                function () {
                    if ($this->pwa()->config('wordpress.autoload', true) === true) {
                        echo '<!-- PWA Manifest -->';
                        echo $this->pwa()->manifest()->metaRegister();
                        echo '<!-- / PWA Manifest -->';
                    }
                },
                1
            );

            add_action(
                'wp_head',
                function () {
                    if ($this->pwa()->config('wordpress.autoload', true) === true) {
                        echo '<!-- PWA MetaTags -->';
                        echo $this->pwa()->manifest()->metaAppleTouchIcon();
                        echo $this->pwa()->manifest()->metaThemeColor();
                        echo '<!-- / PWA MetaTags -->';
                    }
                },
                5
            );

            add_action(
                'wp_head',
                function () {
                    if ($this->pwa()->config('wordpress.autoload', true) === true) {
                        echo '<!-- PWA Global Vars -->';
                        echo $this->pwa()->getGlobalVarsScripts();
                        echo '<!-- / PWA Global Vars -->';
                    }
                },
                25
            );

            add_action(
                'wp_footer',
                function () {
                    if ($this->pwa()->config('wordpress.autoload', true) === true) {
                        echo '<!-- PWA Service Worker Registration -->';
                        echo $this->pwa()->serviceWorker()->getRegisterScripts();
                        echo '<!-- / PWA Service Worker Registration -->';
                    }
                }
            );
            /**/
            $this->setBooted();
        }
    }
}
