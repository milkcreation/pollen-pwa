<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\ResponseInterface;

interface PwaServiceWorkerInterface
{
    /**
     * Ajout de scripts JS au Service Worker.
     *
     * @param string scripts
     *
     * @return static
     */
    public function appendScripts(string $scripts): PwaServiceWorkerInterface;

    /**
     * Récupération des scripts JS de déclaration du Service Worker.
     *
     * @return string
     */
    public function getRegisterScripts(): string;

    /**
     * Réponse HTTP de la définition du Service Worker (au format JAVASCRIPT)
     *
     * @return ResponseInterface
     */
    public function response(): ResponseInterface;


}