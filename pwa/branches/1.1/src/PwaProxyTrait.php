<?php

declare(strict_types=1);

namespace Pollen\Pwa;

use Psr\Container\ContainerInterface as Container;
use RuntimeException;

trait PwaProxyTrait
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
            $container = method_exists($this, 'getContainer') ? $this->getContainer() : null;

            if ($container instanceof Container && $container->has(PwaInterface::class)) {
                $this->pwa = $container->get(PwaInterface::class);
            } else {
                try {
                    $this->pwa = Pwa::getInstance();
                } catch(RuntimeException $e) {
                    $this->pwa = new Pwa();
                }
            }
        }

        return $this->pwa;
    }

    /**
     * Définition du gestionnaire de pwa.
     *
     * @param PwaInterface $pwa
     *
     * @return static
     */
    public function setPwa(PwaInterface $pwa): PwaProxyTrait
    {
        $this->pwa = $pwa;

        return $this;
    }
}
