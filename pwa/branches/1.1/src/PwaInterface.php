<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\EventProxyInterface;
use Pollen\Support\Proxy\HttpRequestProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;
use Pollen\Support\Proxy\RouterProxyInterface;

interface PwaInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface,
    HttpRequestProxyInterface,
    EventProxyInterface,
    PartialProxyInterface,
    RouterProxyInterface
{
    /**
     * Initialisation du gestionnaire d'optimisation.
     *
     * @return static
     */
    public function boot(): PwaInterface;

    /**
     * Récupération de la liste des variables globales.
     *
     * @return array
     */
    public function getGlobalVars(): array;

    /**
     * Récupération des scripts JS de déclaration des variables globales.
     *
     * @return string
     */
    public function getGlobalVarsScripts(): string;

    /**
     * Instance du Manifest.
     *
     * @return PwaManifestInterface
     */
    public function manifest(): PwaManifestInterface;

    /**
     * Instance du Service Worker.
     *
     * @return PwaServiceWorkerInterface
     */
    public function serviceWorker(): PwaServiceWorkerInterface;

    /**
     * Définition de l'adaptateur associé.
     *
     * @param PwaAdapterInterface $adapter
     *
     * @return static
     */
    public function setAdapter(PwaAdapterInterface $adapter): PwaInterface;
}
