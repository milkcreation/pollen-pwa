<?php declare(strict_types=1);

namespace Pollen\Pwa;

use RuntimeException;
use Psr\Container\ContainerInterface as Container;
use Pollen\Pwa\Contracts\PwaAdapterContract;
use Pollen\Pwa\Contracts\PwaManagerContract;
use Pollen\Pwa\Partial\CameraCapturePartial;
use Pollen\Pwa\Partial\InstallPromotionPartial;
use tiFy\Routing\Strategy\AppStrategy;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\Proxy\Partial;
use tiFy\Support\Proxy\Url;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Storage;
use tiFy\Support\ParamsBag;

class Pwa implements PwaManagerContract
{
    use BootableTrait, ContainerAwareTrait;

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
        'controller' => PwaController::class
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
            /** Routage */
            $controller = $this->getContainer()->get(PwaController::class);

            Router::get('/manifest.webmanifest', [$controller, 'manifest'])->strategy('json');
            Router::get('/offline.html', [$controller, 'offline'])->setStrategy(new AppStrategy());
            Router::get('/sw.js', [$controller, 'serviceWorker'])->setStrategy(new AppStrategy());
            /**/

            /** Partials */
            Partial::register('pwa-camera-capture', CameraCapturePartial::class);
            Partial::register('pwa-install-promotion', InstallPromotionPartial::class);
            /**/

            add_action('wp_head', function () {
                echo "<link rel=\"manifest\" href=\"" . Url::root('/manifest.webmanifest')->path() . "\">";
            }, 1);

            add_action('wp_footer', function () {
                echo partial('pwa-install-promotion');
            }, 1);

            $this->setBooted();
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
    public function provider(string $name)
    {
        return $this->config("providers.{$name}", $this->defaultProviders[$name] ?? null);
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
