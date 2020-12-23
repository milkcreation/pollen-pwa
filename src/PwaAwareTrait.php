<?php declare(strict_types=1);

namespace Pollen\Pwa;

trait PwaAwareTrait
{
    /**
     * Instance du gestionnaire de plugin.
     * @var Pwa|null
     */
    protected $pwa;

    /**
     * Récupération de l'instance du gestionnaire de plugin.
     *
     * @return Pwa|null
     */
    public function pwa(): ?Pwa
    {
        return $this->pwa;
    }

    /**
     * Définition du gestionnaire de plugin.
     *
     * @param Pwa $pwa
     *
     * @return static
     */
    public function setPwa(Pwa $pwa): self
    {
        $this->pwa = $pwa;

        return $this;
    }
}