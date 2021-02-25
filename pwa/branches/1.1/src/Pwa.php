<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use RuntimeException;
use Psr\Container\ContainerInterface as Container;
use Pollen\Pwa\Contracts\PwaAdapterContract;
use Pollen\Pwa\Contracts\PwaManagerContract;
use Pollen\Pwa\Controller\PwaController;
use Pollen\Pwa\Controller\PwaOfflineController;
use Pollen\Pwa\Controller\PwaPushController;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\InstallPromotionPartial;
use tiFy\Contracts\Routing\RouteGroup;
use tiFy\Routing\Strategy\AppStrategy;
use tiFy\Routing\Strategy\JsonStrategy;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\Concerns\PartialManagerAwareTrait;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Storage;
use tiFy\Support\ParamsBag;

class Pwa implements PwaManagerContract
{
    use BootableTrait;
    use ContainerAwareTrait;
    use PartialManagerAwareTrait;

    /**
     * Instance de l'extension de gestion d'optimisation de site.
     * @var PwaManagerContract|null
     */
    private static $instance;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    private $configBag;

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Instance de l'adapteur associé
     * @var PwaAdapterContract|null
     */
    protected $adapter;

    /**
     * Liste des services par défaut fournis par conteneur d'injection de dépendances.
     * @var array
     */
    protected $defaultProviders = [
        'controller' => PwaController::class,
    ];

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): PwaManagerContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): PwaManagerContract
    {
        if (!$this->isBooted()) {
            events()->trigger('pwa.booting', [$this]);

            /** Routage */
            // - Worker & Manifest
            Router::get('/manifest.webmanifest', [PwaController::class, 'manifest'])->strategy('json');
            Router::get('/sw.js', [PwaController::class, 'serviceWorker'])->strategy('app');
            // - Offline Page
            Router::get('/offline.html', [PwaOfflineController::class, 'index'])->strategy('app');
            Router::get('/offline.css', [PwaOfflineController::class, 'css'])->strategy('app');
            Router::get('/offline.js', [PwaOfflineController::class, 'js'])->strategy('app');
            // - Push
            // -- Test
            Router::get('/push-test.html', [PwaPushController::class, 'testHtml'])->strategy('app');
            Router::get('/push-test.css', [PwaPushController::class, 'testCss'])->strategy('app');
            Router::get('/push-test.js', [PwaPushController::class, 'testJs'])->strategy('app');
            Router::get('/push-test-service-worker.js', [PwaPushController::class, 'testServiceWorker'])->strategy('app');
            Router::xhr('/push-test-subscription', [PwaPushController::class, 'testSubscriptionXhr']);
            Router::xhr('/push-test-subscription', [PwaPushController::class, 'testSubscriptionXhr'], 'PUT');
            Router::xhr('/push-test-subscription', [PwaPushController::class, 'testSubscriptionXhr'], 'DELETE');
            Router::xhr('/push-test-send', [PwaPushController::class, 'testSendXhr']);

            /** /
            Router::group(
                '/pwa/api',
                function (RouteGroup $router) {
                    $router->get('/', [PwaApiController::class, 'index'])->strategy('json');
                    $router->get('/subscriber', [PwaApiController::class, 'subscriber'])->strategy('json');
                }
            );
            /**/

            /** Partials */
            $this->partialManager()
                ->register('pwa-camera-capture', CameraCapturePartial::class)
                ->register('pwa-install-promotion', InstallPromotionPartial::class);
            /**/

            $this->setBooted();

            events()->trigger('pwa.booted', [$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if ($this->configBag === null) {
            $this->configBag = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->configBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->configBag->set($key);
        } else {
            return $this->configBag;
        }
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources');
        }
        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(PwaAdapterContract $adapter): PwaManagerContract
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): PwaManagerContract
    {
        $this->config($attrs);

        return $this;
    }
}
