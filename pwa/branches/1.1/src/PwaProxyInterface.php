<?php

declare(strict_types=1);

namespace Pollen\Pwa;

interface PwaProxyInterface
{
    /**
     * Récupération de l'instance du gestionnaire de Pwa.
     *
     * @return PwaInterface|null
     */
    public function pwa(): PwaInterface;

    /**
     * Définition du gestionnaire de pwa.
     *
     * @param PwaInterface $pwa
     *
     * @return PwaProxy|static
     */
    public function setPwa(PwaInterface $pwa): PwaProxy;
}
