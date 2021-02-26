<?php

declare(strict_types=1);

namespace Pollen\Pwa;

interface PwaProxyTraitInterface
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
     * @return PwaProxyTrait
     */
    public function setPwa(PwaInterface $pwa): PwaProxyTrait;
}
