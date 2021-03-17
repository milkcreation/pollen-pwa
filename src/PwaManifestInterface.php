<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Http\ResponseInterface;

interface PwaManifestInterface
{
    /**
     * Récupération de la liste des variables par défaut.
     *
     * @return array
     */
    public function defaults(): array;

    /**
     * Récupération de la valeur d'une variable par défaut.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function default(string $key);

    /**
     * Récupération de variables.
     *
     * @param string|null $key
     *
     * @return array|string|bool
     */
    public function getVars(?string $key = null);

    /**
     * Liste des variables au format JSON.
     *
     * @return string
     */
    public function json(): string;

    /**
     * Récupération de la meta-balise de l'icône IOS Safari.
     *
     * @return string
     */
    public function metaAppleTouchIcon(): string;

    /**
     * Récupération de la meta-balise de déclaration du manifest.
     *
     * @return string
     */
    public function metaRegister(): string;

    /**
     * Récupération de la meta-balise de la couleur du thème.
     *
     * @return string
     */
    public function metaThemeColor(): string;

    /**
     * Réponse HTTP du fichier manifest (au format JSON)
     *
     * @return ResponseInterface
     */
    public function response(): ResponseInterface;

    /**
     * Définition de la liste des variables par défaut.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    public function setDefault(string $key, $value): PwaManifestInterface;

    /**
     * Contraint l'utilisation de certaines clés de variables dans le fichier manifest.
     *
     * @param array $keysOnly
     *
     * @return static
     */
    public function setKeysOnly(array $keysOnly): PwaManifestInterface;

    /**
     * Définition d'une liste de variables.
     *
     * @param array
     *
     * @return static
     */
    public function setVars(array $vars): PwaManifestInterface;

    /**
     * Récupération de la liste des variables sous forme d'un tableau.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * @param string $apple_touch_icon
     *
     * @return PwaManifestInterface
     */
    public function setAppleTouchIcon(string $apple_touch_icon): PwaManifestInterface;

    /**
     * @param string $background_color
     *
     * @return PwaManifestInterface
     */
    public function setBackgroundColor(string $background_color): PwaManifestInterface;

    /**
     * @param string $description
     *
     * @return PwaManifestInterface
     */
    public function setDescription(string $description): PwaManifestInterface;

    /**
     * @param string $dir
     *
     * @return PwaManifestInterface
     */
    public function setDir(string $dir): PwaManifestInterface;

    /**
     * @param string $display
     *
     * @return PwaManifestInterface
     */
    public function setDisplay(string $display): PwaManifestInterface;

    /**
     * @param array $icons
     *
     * @return PwaManifestInterface
     */
    public function setIcons(array $icons): PwaManifestInterface;

    /**
     * @param string $lang
     *
     * @return PwaManifestInterface
     */
    public function setLang(string $lang): PwaManifestInterface;

    /**
     * @param string $name
     *
     * @return PwaManifestInterface
     */
    public function setName(string $name): PwaManifestInterface;

    /**
     * @param string $orientation
     *
     * @return PwaManifestInterface
     */
    public function setOrientation(string $orientation): PwaManifestInterface;

    /**
     * @param string $prefer_related_applications
     *
     * @return PwaManifestInterface
     */
    public function setPreferRelatedApplications(string $prefer_related_applications): PwaManifestInterface;

    /**
     * @param string $related_applications
     *
     * @return PwaManifestInterface
     */
    public function setRelatedApplications(string $related_applications): PwaManifestInterface;

    /**
     * @param string $scope
     *
     * @return PwaManifestInterface
     */
    public function setScope(string $scope): PwaManifestInterface;

    /**
     * @param string $short_name
     *
     * @return PwaManifestInterface
     */
    public function setShortName(string $short_name): PwaManifestInterface;

    /**
     * @param string $start_url
     *
     * @return PwaManifestInterface
     */
    public function setStartUrl(string $start_url): PwaManifestInterface;

    /**
     * @param string $theme_color
     *
     * @return PwaManifestInterface
     */
    public function setThemeColor(string $theme_color): PwaManifestInterface;
}