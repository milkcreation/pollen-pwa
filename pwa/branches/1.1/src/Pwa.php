<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\UrlHelper;
use Pollen\Http\UrlManipulator;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\EventProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\Support\Proxy\RouterProxy;
use Pollen\Support\Filesystem;
use Pollen\Pwa\Controller\PwaController;
use Pollen\Pwa\Controller\PwaOfflineController;
use Pollen\Pwa\Controller\PwaPushController;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\InstallPromotionPartial;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;

class Pwa implements PwaInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;
    use EventProxy;
    use PartialProxy;
    use RouterProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance de l'adapteur associé
     * @var PwaAdapterInterface|null
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
     * Chemin vers le répertoire des ressources.
     * @var string|null
     */
    protected $resourcesBaseDir;

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

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): PwaInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): PwaInterface
    {
        if (!$this->isBooted()) {
            $this->event()->trigger('pwa.booting', [$this]);

            /** Routage */
            // - Worker & Manifest
            $wmController = $this->getContainer() ? PwaController::class : new PwaController($this);
            $this->router()->get('/manifest.webmanifest', [$wmController, 'manifest']);
            $this->router()->get('/sw.js', [$wmController, 'serviceWorker']);
            // - Offline Page
            $offlineController = $this->getContainer() ? PwaOfflineController::class : new PwaOfflineController($this);
            $this->router()->get('/offline.html', [$offlineController, 'index']);
            $this->router()->get('/offline.css', [$offlineController, 'css']);
            $this->router()->get('/offline.js', [$offlineController, 'js']);
            // - Push
            // -- Test
            $pushController = $this->getContainer() ? PwaPushController::class : new PwaPushController($this);
            $this->router()->get('/push-test.html', [$pushController, 'testHtml']);
            $this->router()->get('/push-test.css', [$pushController, 'testCss']);
            $this->router()->get('/push-test.js', [$pushController, 'testJs']);
            $this->router()->get('/push-test-service-worker.js', [$pushController, 'testServiceWorker']);
            $this->router()->get('/push-test-subscription', [$pushController, 'testSubscriptionXhr']);
            $this->router()->xhr('/push-test-subscription', [$pushController, 'testSubscriptionXhr']);
            $this->router()->xhr('/push-test-subscription', [$pushController, 'testSubscriptionXhr'], 'PUT');
            $this->router()->xhr(
                '/push-test-subscription',
                [$pushController, 'testSubscriptionXhr'],
                'DELETE'
            );
            $this->router()->xhr('/push-test-send', [$pushController, 'testSendXhr']);

            /** /
             * Router::group(
             * '/pwa/api',
             * function (RouteGroup $router) {
             * $router->get('/', [PwaApiController::class, 'index'])->strategy('json');
             * $router->get('/subscriber', [PwaApiController::class, 'subscriber'])->strategy('json');
             * }
             * );
             * /**/

            /** Partials */
            $this->partial()
                ->register(
                    'pwa-camera-capture',
                    $this->containerHas(CameraCapturePartial::class)
                        ? CameraCapturePartial::class : new CameraCapturePartial($this, $this->partial())
                )
                ->register(
                    'pwa-install-promotion',
                    $this->containerHas(InstallPromotionPartial::class)
                        ? InstallPromotionPartial::class : new InstallPromotionPartial($this, $this->partial())
                );

            $this->setBooted();

            $this->event()->trigger('pwa.booted', [$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultConfig(): array
    {
        $urlHelper = new UrlHelper();
        $startUrl = $urlHelper->getRelativePath('/');
        $startUrl = (new UrlManipulator($startUrl))->with(
            [
                'utm_medium' => 'PWA',
                'utm_source' => 'standalone',
            ]
        );

        return [
            // @see https://developer.mozilla.org/en-US/docs/Web/Manifest
            'manifest' => [
                'name'                 => get_bloginfo('name'),
                'short_name'           => get_bloginfo('name'),
                'icons'                => [
                    [
                        'src'     => $urlHelper->getRelativePath($this->resources('/assets/dist/img/192.png')),
                        'sizes'   => '192x192',
                        'type'    => 'image/png',
                        'purpose' => 'any maskable',
                    ],
                    [
                        'src'     => $urlHelper->getRelativePath($this->resources('/assets/dist/img/512.png')),
                        'sizes'   => '512x512',
                        'type'    => 'image/png',
                        'purpose' => 'any maskable',
                    ],
                ],
                'scope'                => $urlHelper->getScope(),
                'start_url'            => (string)$startUrl,
                'display'              => 'standalone',
                'background_color'     => '#5A0FC8',
                'theme_color'          => '#FFFFFF',
                'related_applications' => [
                    [
                        'platform' => 'webapp',
                        'url'      => $urlHelper->getAbsoluteUrl('/manifest.webmanifest'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null): string
    {
        if ($this->resourcesBaseDir === null) {
            $this->resourcesBaseDir = Filesystem::normalizePath(
                realpath(
                    dirname(__DIR__) . '/resources/'
                )
            );

            if (!file_exists($this->resourcesBaseDir)) {
                throw new RuntimeException('Recaptcha ressources directory unreachable');
            }
        }

        return is_null($path) ? $this->resourcesBaseDir : $this->resourcesBaseDir . Filesystem::normalizePath($path);
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(PwaAdapterInterface $adapter): PwaInterface
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setResourcesBaseDir(string $resourceBaseDir): PwaInterface
    {
        $this->resourcesBaseDir = Filesystem::normalizePath($resourceBaseDir);

        return $this;
    }
}
