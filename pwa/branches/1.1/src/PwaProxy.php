<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Pollen\Support\StaticProxy;
use RuntimeException;

trait PwaProxy
{
    /**
     * Instance du gestionnaire de Pwa.
     * @var PwaInterface|null
     */
    private $pwa;

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
                $this->pwa = StaticProxy::getProxyInstance(
                    PwaInterface::class,
                    Pwa::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        return $this->pwa;
    }

    /**
     * Définition du gestionnaire de pwa.
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
