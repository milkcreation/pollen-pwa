<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Support\ProxyResolver;
use RuntimeException;

/**
 * @see PwaProxyInterface
 */
trait PwaProxy
{
    private ?PwaInterface $pwa = null;

    /**
     * Récupération de l'instance du gestionnaire de Pwa.
     *
     * @return PwaInterface
     */
    public function pwa(): PwaInterface
    {
        if ($this->pwa === null) {
            try {
                $this->pwa = Pwa::getInstance();
            } catch (RuntimeException $e) {
                $this->pwa = ProxyResolver::getInstance(
                    PwaInterface::class,
                    Pwa::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        return $this->pwa;
    }

    /**
     * Définition du gestionnaire de Pwa.
     *
     * @param PwaInterface $pwa
     *
     * @return void
     */
    public function setPwa(PwaInterface $pwa): void
    {
        $this->pwa = $pwa;
    }
}
