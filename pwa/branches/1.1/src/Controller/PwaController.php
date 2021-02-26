<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Http\JsonResponseInterface;
use Pollen\Http\ResponseInterface;

class PwaController extends AbstractController
{
    /**
     * Manifest
     *
     * @return JsonResponseInterface
     */
    public function manifest(): JsonResponseInterface
    {
        return $this->json($this->pwa()->config('manifest', []));
    }

    /**
     * Service Worker
     *
     * @return ResponseInterface
     */
    public function serviceWorker(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/service-worker/sw.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }
}
