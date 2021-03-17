<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\ResponseInterface;

interface PwaServiceWorkerInterface
{
    /**
     * Réponse HTTP de la définition du Service Worker (au format JAVASCRIPT)
     *
     * @return ResponseInterface
     */
    public function response(): ResponseInterface;

    /**
     * Récupération des scripts JS de déclaration du Service Worker.
     *
     * @return string
     */
    public function getRegisterScripts(): string;
}