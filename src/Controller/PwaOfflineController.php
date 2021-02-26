<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Http\ResponseInterface;

class PwaOfflineController extends AbstractController
{
    /**
     * Page Html
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->view('index', $this->params()->all());
    }

    /**
     * Css
     *
     * @return ResponseInterface
     */
    public function css(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/css/offline/offline.css'));

        return $this->response($content, 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Js
     *
     * @return ResponseInterface
     */
    public function js(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/offline/offline.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * @inheritDoc
     */
    public function viewEngineDirectory(): string
    {
        return $this->pwa()->resources('/views/offline');
    }
}
