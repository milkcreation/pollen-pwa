<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\UrlHelper;
use Pollen\Pwa\Adapters\WpPwaAdapter;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\EventProxy;
use Pollen\Support\Proxy\HttpRequestProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\Support\Proxy\RouterProxy;
use Pollen\Routing\RouteInterface;
use Pollen\Pwa\Controller\PwaController;
use Pollen\Pwa\Controller\PwaOfflineController;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\PwaInstallerPartial;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;
use Throwable;

class Pwa implements PwaInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use ContainerProxy;
    use EventProxy;
    use HttpRequestProxy;
    use PartialProxy;
    use RouterProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static ?PwaInterface $instance = null;

    /**
     * Instance de l'adapteur associé
     * @var PwaAdapterInterface|null
     */
    protected ?PwaAdapterInterface $adapter = null;

    /**
     * Liste des services par défaut fournis par conteneur d'injection de dépendances.
     * @var array<string, string>
     */
    protected array $defaultProviders = [
        'controller' => PwaController::class,
    ];

    /**
     * Instance du manifest.
     * @var PwaManifestInterface|null
     */
    protected ?PwaManifestInterface $manifest = null;

    /**
     * Instance du service worker.
     * @var PwaServiceWorkerInterface|null
     */
    protected ?PwaServiceWorkerInterface $serviceWorker = null;

    /**
     * @var array<string, RouteInterface>
     */
    protected array $routes = [];

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }

        $this->boot();
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
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): PwaInterface
    {
        if (!$this->isBooted()) {
            $this->event()->trigger('pwa.booting', [$this]);

            /** Partials */
            $this->partial()
                ->register(
                    'pwa-camera-capture',
                    $this->containerHas(CameraCapturePartial::class)
                        ? CameraCapturePartial::class : new CameraCapturePartial($this, $this->partial())
                )
                ->register(
                    'pwa-installer',
                    $this->containerHas(PwaInstallerPartial::class)
                        ? PwaInstallerPartial::class : new PwaInstallerPartial($this, $this->partial())
                );

            /** Routage */
            $routePrefix = '_pwa';

            // - Worker & Manifest
            $wmController = $this->getContainer() ? PwaController::class : new PwaController($this);
            $this->routes['manifest'] = $this->router()->get("$routePrefix/manifest.webmanifest", [$wmController, 'manifest']);
            $this->routes['icon'] = $this->router()->get("$routePrefix/icons/{icon}", [$wmController, 'icon']);
            $this->routes['service-worker'] = $this->router()->get("$routePrefix/sw.js", [$wmController, 'serviceWorker']);
            $this->routes['register'] = $this->router()->get("$routePrefix/register.js", [$wmController, 'register']);

            // - Offline Page
            $offlineController = $this->getContainer() ? PwaOfflineController::class : new PwaOfflineController($this);
            $this->routes['offline.html'] = $this->router()->get("$routePrefix/offline.html", [$offlineController, 'index']);
            $this->routes['offline.css'] = $this->router()->get("$routePrefix/offline.css", [$offlineController, 'css']);
            $this->routes['offline.js'] = $this->router()->get("$routePrefix/offline.js", [$offlineController, 'js']);

            /** Initialisation de l'adapteur Wordpress */
            if ($this->adapter === null && defined('WPINC')) {
                $this->setAdapter(new WpPwaAdapter($this));
            }

            $this->setBooted();

            $this->event()->trigger('pwa.booted', [$this]);
        }

        return $this;
    }

    /**
     * Récupération d'une route.
     *
     * @param string $endpoint
     *
     * @return RouteInterface
     */
    protected function getEndpointRoute(string $endpoint): RouteInterface
    {
        if ($route = $this->routes[$endpoint]) {
            return $route;
        }
        throw new RuntimeException(sprintf('Pwa Route for endpoint [%s] is unavailable.', $endpoint));
    }

    /**
     * @inheritDoc
     */
    public function getEndpointUrl(string $endpoint, array $args = [], $isAbsolute = false): string
    {
        return $this->router()->getRouteUrl($this->getEndpointRoute($endpoint), $args, $isAbsolute);
    }

    /**
     * @inheritDoc
     */
    public function getGlobalVars(bool $inline = true): array
    {
        $vars = [];

        $urlHelper = new UrlHelper();
        $vars['url'] = $this->getEndpointUrl('service-worker', [], true);

        $host = $this->httpRequest()->getHttpHost();
        $base = ltrim(rtrim(str_replace('/', '-', $this->httpRequest()->getRewriteBase()), '-'), '-');
        $vars['cache'] = [
            'enabled'   => true,
            'key'       => "$host-$base-pwa-1.0.0",
            'whitelist' => [
                $this->getEndpointUrl('offline.html'),
                $urlHelper->getRelativePath('/?utm_medium=PWA&utm_source=standalone'),
            ],
            'blacklist' => ['/\/wp-admin/', '/\/wp-login/', '/preview=true/'],
        ];

        $vars['offline_url'] = $this->getEndpointUrl('offline.html');

        $vars['navigation_preload'] = false;

        $vars['app'] = [
            'url'   => $urlHelper->getAbsoluteUrl(),
            'scope' => $urlHelper->getRelativePath(''),
        ];

        return $vars;
    }

    /**
     * @inheritDoc
     */
    public function getGlobalVarsScripts(): string
    {
        $vars = $this->getGlobalVars();

        try {
            $vars = json_encode($vars, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            $vars = '{}';
        }

        $jsVars = "const PWA=$vars";

        return "<script type=\"text/javascript\">/* <![CDATA[ */$jsVars/* ]]> */</script>";
    }

    /**
     * @inheritDoc
     */
    public function manifest(): PwaManifestInterface
    {
        if ($this->manifest === null) {
            $this->manifest = $this->containerHas(PwaManifestInterface::class)
                ? $this->containerGet(PwaManifestInterface::class) : new PwaManifest([], $this);
            $this->manifest()->setVars($this->config('manifest', []));
        }

        return $this->manifest;
    }

    /**
     * @inheritDoc
     */
    public function serviceWorker(): PwaServiceWorkerInterface
    {
        if ($this->serviceWorker === null) {
            $this->serviceWorker = $this->containerHas(PwaServiceWorkerInterface::class)
                ? $this->containerGet(PwaServiceWorkerInterface::class) : new PwaServiceWorker($this);
        }

        return $this->serviceWorker;
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(PwaAdapterInterface $adapter): PwaInterface
    {
        $this->adapter = $adapter;

        return $this;
    }
}
