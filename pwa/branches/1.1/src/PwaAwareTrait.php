<?php declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Pwa\Contracts\PwaManagerContract;

trait PwaAwareTrait
{
    /**
     * Instance du gestionnaire de plugin.
     * @var PwaManagerContract|null
     */
    protected $pwa;

    /**
     * Récupération de l'instance du gestionnaire de plugin.
     *
     * @return PwaManagerContract|null
     */
    public function pwa(): ?PwaManagerContract
    {
        return $this->pwa;
    }

    /**
     * Définition du gestionnaire de plugin.
     *
     * @param PwaManagerContract $pwa
     *
     * @return static
     */
    public function setPwaManager(PwaManagerContract $pwa): self
    {
        $this->pwa = $pwa;

        return $this;
    }
}
