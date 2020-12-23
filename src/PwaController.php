<?php declare(strict_types=1);

namespace Pollen\Pwa;

use tiFy\Contracts\Http\Response;
use tiFy\Contracts\View\Engine;
use tiFy\Routing\BaseController;
use tiFy\Support\Proxy\Url;
use tiFy\Support\Proxy\View;

class PwaController extends BaseController
{
    use PwaAwareTrait;

    /**
     * Manifest
     *
     * @return array
     */
    public function manifest(): array
    {
        return [
            "name"                 => get_bloginfo('name'),
            "short_name"           => get_bloginfo('name'),
            "icons"                => [
                [
                    "src"     => Url::root($this->pwa()->resources()->rel("assets/images/192.png"))->path(),
                    "sizes"   => "192x192",
                    "type"    => "image/png",
                    "purpose" => "any maskable"
                ],
                [
                    "src"     => Url::root($this->pwa()->resources()->rel("assets/images/512.png"))->path(),
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
        ];
    }

    /**
     * Page Hors ligne
     *
     * @return Response
     */
    public function offline()
    {
        return $this->view('app/offline', [
            'css' => $this->getOfflineCss()
        ]);
    }

    /**
     * Css Page Hors ligne
     *
     * @return string
     */
    protected function getOfflineCss(): string
    {
        return file_get_contents($this->pwa()->resources()->path('assets/css/app/offline.css'));
    }

    /**
     * Service Worker
     *
     * @return Response
     */
    public function serviceWorker(): Response
    {
        $content = file_get_contents($this->pwa()->resources()->path('assets/js/sw.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * Moteur d'affichage des gabarits d'affichage.
     *
     * @return Engine
     */
    public function viewEngine(): Engine
    {
        return View::getPlatesEngine([
            'directory' => $this->pwa()->resources('views')
        ]);
    }
}
