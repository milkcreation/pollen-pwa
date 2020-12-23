<?php declare(strict_types=1);

namespace Pollen\Pwa;

use tiFy\Container\ServiceProvider;
use Pollen\Pwa\Api\PwaApi;
use Pollen\Pwa\Contracts\Pwa as PwaManagerContract;
use Pollen\Pwa\Push\PwaPushSend;
use Pollen\Pwa\Partial\InstallPromotionPartial;
use tiFy\Support\Proxy\Partial;

class PwaServiceProvider extends ServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        PwaManagerContract::class,
        'pwa.api',
        'pwa.controller',
        'pwa.push.send',
        'pwa.push.subscriber',
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        events()->listen('wp.booted', function () {
            $this->getContainer()->get(EmbedContract::class);
        });
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->share(PwaManagerContract::class, function () {
            return new Pwa(config('pwa', []), $this->getContainer());
        });

        $this->getContainer()->share('pwa.api', function () {
            return new PwaApi();
        });

        $this->getContainer()->share('pwa.controller', function () {
            /** @var Pwa $manager */
            $manager = $this->getContainer()->get('pwa');

            $provider = $manager->provider('controller');
            if (!is_object($provider)) {
                $provider = new $provider;
            }

            $provider = $provider instanceof PwaController ? $provider : new PwaController();

            return $provider->setPwa($manager);
        });

        $this->getContainer()->share('pwa.push.send', function () {
            return new PwaPushSend();
        });
    }
}