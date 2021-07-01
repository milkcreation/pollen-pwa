<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Support\Proxy\EventProxyInterface;

interface PwaServiceWorkerInterface extends EventProxyInterface, PwaProxyInterface
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
     * Récupération de la liste des scripts.
     *
     * @return array
     */
    public function getAppendedScripts(): array;

    /**
     * Récupération des scripts JS de déclaration du Service Worker.
     *
     * @return string
     */
    public function getRegisterScripts(): string;
}