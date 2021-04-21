<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\Response;
use Pollen\Http\ResponseInterface;
use Pollen\Http\UrlHelper;
use Pollen\Http\UrlManipulator;
use Throwable;

/**
 * @see https://developer.mozilla.org/fr/docs/Web/Manifest
 */
class PwaManifest implements PwaManifestInterface
{
    use PwaProxy;

    /**
     * Liste des variables par défaut.
     * @var array|null
     */
    private $defaults;

    /**
     * Liste des variables déclarés.
     * @var array
     */
    private $vars;

    /**
     * Listes des clés de qualification des variables.
     * @var array
     */
    private $varKeys = [
        'background_color',
        'description',
        'dir',
        'display',
        'icons',
        'lang',
        'name',
        'orientation',
        'prefer_related_applications',
        'related_applications',
        'scope',
        'short_name',
        'start_url',
        'theme_color'
    ];

    /**
     * Icône IOS Safari.
     * @var string
     */
    protected $apple_touch_icon;

    /**
     * Couleur de fond attendue pour l'application web.
     * @var string
     */
    protected $background_color;

    /**
     * Description générale de ce que fait l'application web.
     * @var string
     */
    protected $description;

    /**
     * Direction du texte pour le nom, le nom court et les membres de description.
     * @var string ltr|rtl|auto
     */
    protected $dir;

    /**
     * Mode d'affichage préféré du développeur pour l'application web.
     * @var string fullscreen|standalone|minimal-ui|browser
     */
    protected $display;

    /**
     * Ensemble d'images qui peuvent servir d'icônes pour l'application dans différents contextes.
     * @var array
     */
    protected $icons;

    /**
     * Langue principale pour les valeurs des membres name et short_name.
     * @var string
     */
    protected $lang;

    /**
     * Nom de qualification de l'application, lisible pour un humain, car il est destiné à être affiché à l'utilisateur.
     * @var string
     */
    protected $name;

    /**
     * Orientation par défaut pour tout le premier niveau d'applications
     * @var string
     *     any|natural|landscape|landscape-primary|landscape-secondary|portrait|portrait-primary|portrait-secondary
     */
    protected $orientation;

    /**
     * Valeur booléenne qui indique à l'agent utilisateur si une application liée doit être préférée à l'application web.
     * @var bool
     */
    protected $prefer_related_applications;

    /**
     * Ensemble d'objets d'application représentant les applications natives installables par la plate-forme
     * sous-jacente ou accessibles à cette plate-forme.
     * @var array
     */
    protected $related_applications;

    /**
     * "scope" (portée) de navigation du contexte applicatif de cette application web.
     * @var string
     */
    protected $scope;

    /**
     * Nom court pour l'application web, compréhensible pour un humain.
     * @var string
     */
    protected $short_name;

    /**
     * URL qui se charge lorsque l'utilisateur lance une application à partir d'un périphérique
     * @var string
     */
    protected $start_url;

    /**
     * Couleur du thème par défaut pour une application.
     * @var string
     */
    protected $theme_color;

    /**
     * Activation de la contrainte des clés de variables de qualification.
     * @var bool
     */
    protected $isVarKeysConstraint = true;

    /**
     * @param array $vars
     * @param PwaInterface|null $pwa
     */
    public function __construct(array $vars = [], ?PwaInterface $pwa = null)
    {
        if ($pwa !== null) {
            $this->setPwa($pwa);
        }

        $this->setVars($vars);
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        $urlHelper = new UrlHelper();

        if (!isset($this->defaults['name'])) {
            $this->defaults['name'] = $this->pwa()->httpRequest()->getHttpHost();
        }

        if (!isset($this->defaults['short_name'])) {
            $this->defaults['short_name'] = $this->pwa()->httpRequest()->getHttpHost();
        }

        if (!isset($this->defaults['theme_color'])) {
            $this->defaults['theme_color'] = '#5A0FC8';
        }

        if (!isset($this->defaults['background_color'])) {
            $this->defaults['background_color'] = '#5A0FC8';
        }

        if (!isset($this->defaults['orientation'])) {
            $this->defaults['orientation'] = 'portrait';
        }

        if (!isset($this->defaults['display'])) {
            $this->defaults['display'] = 'standalone';
        }

        if (!isset($this->defaults['icons'])) {
            $this->defaults['icons'] = [
                [
                    'src'     => $urlHelper->getAbsoluteUrl(
                        $this->pwa()->resources('/assets/dist/img/192.png')
                    ),
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src'     => $urlHelper->getAbsoluteUrl(
                        $this->pwa()->resources('/assets/dist/img/512.png')
                    ),
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ];
        }

        if (!isset($this->defaults['start_url'])) {
            $startUrl = $urlHelper->getRelativePath('/');
            $startUrl = (new UrlManipulator($startUrl))->with(
                [
                    'utm_medium' => 'PWA',
                    'utm_source' => 'standalone',
                ]
            );

            $this->defaults['start_url'] = (string)$startUrl;
        }

        if (!isset($this->defaults['scope'])) {
            $this->defaults['scope'] = $urlHelper->getScope();
        }

        if (!isset($this->defaults['related_applications'])) {
            $this->defaults['related_applications'] = [
                [
                    'platform' => 'webapp',
                    'url'      => $urlHelper->getAbsoluteUrl('/manifest.webmanifest'),
                ],
            ];
        }

        if (!isset($this->defaults['apple_touch_icon'])) {
            $this->defaults['apple_touch_icon'] = $urlHelper->getAbsoluteUrl(
                $this->pwa()->resources('/assets/dist/img/192.png')
            );
        }

        return $this->defaults;
    }

    /**
     * @inheritDoc
     */
    public function default(string $key)
    {
        return $this->defaults[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getVars(?string $key = null)
    {
        $this->vars = $this->defaults();

        foreach ($this->varKeys as $varKey) {
            if (isset($this->{$varKey})) {
                $this->vars[$varKey] = $this->{$varKey};
            }
        }

        if ($key === null) {
            return $this->vars;
        }

        return $this->vars[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function json(): string
    {
        try {
            return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            return '{}';
        }
    }

    /**
     * @inheritDoc
     */
    public function metaAppleTouchIcon(): string
    {
        return ($icon = $this->getVars('apple_touch_icon')) ? "<link rel=\"apple-touch-icon\" href=\"$icon\"/>" : '';
    }

    /**
     * @inheritDoc
     */
    public function metaRegister(): string
    {
        return "<link rel=\"manifest\" href=\"" . (new UrlHelper())->getRelativePath('/manifest.webmanifest') . "\">";
    }

    /**
     * @inheritDoc
     */
    public function metaThemeColor(): string
    {
        return ($color = $this->getVars('theme_color')) ? "<meta name=\"theme-color\" content=\"$color\"/>" : '';
    }

    /**
     * @inheritDoc
     */
    public function resetVars(): void
    {
        $this->vars = null;
    }

    /**
     * @inheritDoc
     */
    public function response(): ResponseInterface
    {
        return new Response($this->json(), 200, ['Content-Type' => 'application/manifest+json']);
    }

    /**
     * @inheritDoc
     */
    public function setDefault(string $key, $value): PwaManifestInterface
    {
        $this->resetVars();

        $this->defaults[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAppleTouchIcon(string $apple_touch_icon): PwaManifestInterface
    {
        $this->resetVars();

        $this->apple_touch_icon = $apple_touch_icon;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBackgroundColor(string $background_color): PwaManifestInterface
    {
        $this->resetVars();

        $this->background_color = $background_color;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): PwaManifestInterface
    {
        $this->resetVars();

        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDir(string $dir): PwaManifestInterface
    {
        $this->resetVars();

        $this->dir = $dir;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDisplay(string $display): PwaManifestInterface
    {
        $this->resetVars();

        $this->display = $display;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIcons(array $icons): PwaManifestInterface
    {
        $this->resetVars();

        $this->icons = $icons;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLang(string $lang): PwaManifestInterface
    {
        $this->resetVars();

        $this->lang = $lang;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): PwaManifestInterface
    {
        $this->resetVars();

        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOrientation(string $orientation): PwaManifestInterface
    {
        $this->resetVars();

        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPreferRelatedApplications(string $prefer_related_applications): PwaManifestInterface
    {
        $this->resetVars();

        $this->prefer_related_applications = $prefer_related_applications;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRelatedApplications(string $related_applications): PwaManifestInterface
    {
        $this->resetVars();

        $this->related_applications = $related_applications;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScope(string $scope): PwaManifestInterface
    {
        $this->resetVars();

        $this->scope = $scope;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setShortName(string $short_name): PwaManifestInterface
    {
        $this->resetVars();

        $this->short_name = $short_name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStartUrl(string $start_url): PwaManifestInterface
    {
        $this->resetVars();

        $this->start_url = $start_url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setThemeColor(string $theme_color): PwaManifestInterface
    {
        $this->resetVars();

        $this->theme_color = $theme_color;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setVars(array $vars): PwaManifestInterface
    {
        foreach ($vars as $k => $v) {
            $method = 'set' . implode('', array_map('ucfirst', explode('_', $k)));
            if (method_exists($this, $method)) {
                $this->$method($v);
            } else {
                $this->$k = $v;
            }

            if (!in_array($k, $this->varKeys, true)) {
                $this->varKeys[] = $k;
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->getVars();
    }
}