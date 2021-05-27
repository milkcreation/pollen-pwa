<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Container\BootableServiceProvider;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Pwa\Adapters\WpPwaAdapter;
use Pollen\Pwa\Controller\PwaController;
use Pollen\Pwa\Controller\PwaOfflineController;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\PwaInstallerPartial;

class PwaServiceProvider extends BootableServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        CameraCapturePartial::class,
        PwaInstallerPartial::class,
        PwaController::class,
        PwaInterface::class,
        PwaManifestInterface::class,
        PwaOfflineController::class,
        WpPwaAdapter::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            PwaInterface::class,
            function () {
                return new Pwa([], $this->getContainer());
            }
        );

        $this->registerAdapters();
        $this->registerControllers();
        $this->registerPartialDrivers();

        $this->getContainer()->share(
            PwaManifestInterface::class,
            function () {
                return new PwaManifest([], $this->getContainer()->get(PwaInterface::class));
            }
        );

        $this->getContainer()->share(
            PwaServiceWorkerInterface::class,
            function () {
                return new PwaServiceWorker($this->getContainer()->get(PwaInterface::class));
            }
        );
    }

    /**
     * Déclaration des adapteurs.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(
            WpPwaAdapter::class,
            function () {
                return new WpPwaAdapter($this->getContainer()->get(PwaInterface::class));
            }
        );
    }

    /**
     * Déclaration des controleurs.
     *
     * @return void
     */
    public function registerControllers(): void
    {
        $this->getContainer()->share(
            PwaController::class,
            function () {
                return new PwaController(
                    $this->getContainer()->get(PwaInterface::class),
                    $this->getContainer()
                );
            }
        );

        $this->getContainer()->share(
            PwaOfflineController::class,
            function () {
                return new PwaOfflineController(
                    $this->getContainer()->get(PwaInterface::class),
                    $this->getContainer()
                );
            }
        );
    }

    /**
     * Déclaration des pilotes de portions d'affichage.
     *
     * @return void
     */
    public function registerPartialDrivers(): void
    {
        $this->getContainer()->add(
            CameraCapturePartial::class,
            function () {
                return new CameraCapturePartial(
                    $this->getContainer()->get(PwaInterface::class),
                    $this->getContainer()->get(PartialManagerInterface::class)
                );
            }
        );

        $this->getContainer()->add(
            PwaInstallerPartial::class,
            function () {
                return new PwaInstallerPartial(
                    $this->getContainer()->get(PwaInterface::class),
                    $this->getContainer()->get(PartialManagerInterface::class)
                );
            }
        );
    }
}
