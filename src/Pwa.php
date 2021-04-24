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
use Pollen\Pwa\Controller\PwaController;
use Pollen\Pwa\Controller\PwaOfflineController;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\PwaInstallerPartial;
use Psr\Container\ContainerInterface as Container;
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
     * Instance du manifest.
     * @var PwaManifestInterface|null
     */
    protected $manifest;

    /**
     * Instance du service worker.
     * @var PwaServiceWorkerInterface|null
     */
    protected $serviceWorker;

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
            // - Worker & Manifest
            $wmController = $this->getContainer() ? PwaController::class : new PwaController($this);
            $this->router()->get('/manifest.webmanifest', [$wmController, 'manifest']);
            $this->router()->get('/sw.js', [$wmController, 'serviceWorker']);
            // - Offline Page
            $offlineController = $this->getContainer() ? PwaOfflineController::class : new PwaOfflineController($this);
            $this->router()->get('/offline.html', [$offlineController, 'index']);
            $this->router()->get('/offline.css', [$offlineController, 'css']);
            $this->router()->get('/offline.js', [$offlineController, 'js']);

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
     * @inheritDoc
     */
    public function getGlobalVars(bool $inline = true): array
    {
        $vars = [];

        $urlHelper = new UrlHelper();
        $vars['url'] = $urlHelper->getAbsoluteUrl('/sw.js');

        $host = $this->httpRequest()->getHttpHost();
        $base = ltrim(rtrim(str_replace('/', '-', $this->httpRequest()->getRewriteBase()), '-'), '-');
        $vars['cache'] = [
            'enabled'   => true,
            'key'       => "$host-$base-pwa-1.0.0",
            'whitelist' => [
                $urlHelper->getRelativePath('offline.html'),
                $urlHelper->getRelativePath('/?utm_medium=PWA&utm_source=standalone')
            ],
            'blacklist' => ['/\/wp-admin/', '/\/wp-login/', '/preview=true/'],
        ];

        $vars['offline_url'] = $urlHelper->getRelativePath('/offline.html');

        $vars['navigation_preload'] = false;

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
