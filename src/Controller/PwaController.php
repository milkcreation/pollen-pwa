<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use tiFy\Contracts\Http\Response;
use tiFy\Support\Proxy\Url;

class PwaController extends AbstractController
{
    /**
     * Manifest
     *
     * @return array
     */
    public function manifest(): array
    {
        return array_merge([
            "name"                 => get_bloginfo('name'),
            "short_name"           => get_bloginfo('name'),
            "icons"                => [
                [
                    "src"     => Url::root($this->pwa()->resources()->rel("assets/dist/img/192.png"))->path(),
                    "sizes"   => "192x192",
                    "type"    => "image/png",
                    "purpose" => "any maskable"
                ],
                [
                    "src"     => Url::root($this->pwa()->resources()->rel("assets/dist/img/512.png"))->path(),
                    "sizes"   => "512x512",
                    "type"    => "image/png",
                    "purpose" => "any maskable"
                ],
            ],
            "scope"                => Url::scope(),
            "start_url"            => Url::root('/')->path() . "?utm_medium=PWA&utm_source=standalone",
            "display"              => "standalone",
            "background_color"     => "#5A0FC8",
            "theme_color"          => "#FFFFFF",
            "related_applications" => [
                [
                    "platform" => "webapp",
                    "url"      => Url::root('/manifest.webmanifest')->render(),
                ]
            ]
        ], $this->pwa()->config('manifest', []));
    }

    /**
     * Service Worker
     *
     * @return Response
     */
    public function serviceWorker(): Response
    {
        $content = file_get_contents($this->pwa()->resources('assets/dist/js/service-worker.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }
}
