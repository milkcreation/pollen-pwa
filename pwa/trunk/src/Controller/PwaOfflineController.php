<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use tiFy\Contracts\Http\Response;
use tiFy\Contracts\View\Engine;
use tiFy\Support\Proxy\View;

class PwaOfflineController extends AbstractController
{
    /**
     * Page Html
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->view('index', $this->all());
    }

    /**
     * Css
     *
     * @return Response
     */
    public function css(): Response
    {
        $content = file_get_contents($this->pwa()->resources('assets/dist/css/offline/offline.css'));

        return $this->response($content, 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Js
     *
     * @return Response
     */
    public function js(): Response
    {
        $content = file_get_contents($this->pwa()->resources('assets/dist/js/offline/offline.js'));

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
            'directory' => $this->pwa()->resources('views/offline')
        ]);
    }
}
