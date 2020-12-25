<?php declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Pwa\Contracts\PwaManagerContract;
use Pollen\Pwa\Adapters\WordpressAdapter;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\InstallPromotionPartial;
use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Partial\Partial as PartialManager;

class PwaServiceProvider extends ServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        CameraCapturePartial::class,
        InstallPromotionPartial::class,
        PwaController::class,
        PwaManagerContract::class,
        WordpressAdapter::class
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        events()->listen('wp.booted', function () {
            /** @var PwaManagerContract $pwa */
            $pwa = $this->getContainer()->get(PwaManagerContract::class);
            $pwa->setAdapter($this->getContainer()->get(WordpressAdapter::class))->boot();
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(PwaManagerContract::class, function () {
            return new Pwa(config('pwa', []), $this->getContainer());
        });

        $this->registerAdapters();
        $this->registerControllers();
        $this->registerPartialDrivers();
    }

    /**
     * Déclaration des adapteurs.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(WordpressAdapter::class, function () {
            return new WordpressAdapter($this->getContainer()->get(PwaManagerContract::class));
        });
    }

    /**
     * Déclaration des controleurs.
     *
     * @return void
     */
    public function registerControllers(): void
    {
        $this->getContainer()->share(PwaController::class, function () {
            return new PwaController(
                $this->getContainer()->get(PwaManagerContract::class),
                $this->getContainer()
            );
        });
    }

    /**
     * Déclaration des pilotes de portions d'affichage.
     *
     * @return void
     */
    public function registerPartialDrivers(): void
    {
        $this->getContainer()->add(CameraCapturePartial::class, function () {
            return new CameraCapturePartial(
                $this->getContainer()->get(PwaManagerContract::class),
                $this->getContainer()->get(PartialManager::class)
            );
        });

        $this->getContainer()->add(InstallPromotionPartial::class, function () {
            return new InstallPromotionPartial(
                $this->getContainer()->get(PwaManagerContract::class),
                $this->getContainer()->get(PartialManager::class)
            );
        });
    }
}
