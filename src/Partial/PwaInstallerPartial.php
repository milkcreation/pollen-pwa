<?php

declare(strict_types=1);

namespace Pollen\Pwa\Partial;

class PwaInstallerPartial extends AbstractPwaPartial
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'classes' => [
                    'title'   => 'PwaInstaller-title',
                    'content' => 'PwaInstaller-content',
                    'button'  => 'PwaButton PwaButton--1 PwaButton--alt PwaInstaller-button',
                    'close'   => 'PwaInstaller-close',
                    'handler' => 'PwaInstaller-handler',
                ],
                /**
                 * @var string fixed|fixed-bottom
                 */
                'style'   => 'fixed',
                'title'   => 'Installer l\'application',
                'content' => 'L\'installation n\'utilise quasiment aucun stockage et offre un moyen rapide et facile' .
                    ' de revenir à cette application.',
                'button'  => 'Installer',
                'close'   => '&#x2715;',
                'timeout' => 5000,
                'handler' => file_get_contents($this->pwa()->resources('assets/dist/img/install-ico.svg')),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultClasses = [
            'title'   => 'PwaInstaller-title',
            'content' => 'PwaInstaller-content',
            'button'  => 'PwaButton PwaButton--1 PwaButton--alt PwaInstaller-button',
            'close'   => 'PwaInstaller-close',
            'handler' => 'PwaInstaller-handler',
        ];

        foreach ($defaultClasses as $key => $class) {
            $this->set("classes.$key", sprintf($this->get("classes.$key", '%s'), $class));
        }

        $this->set(
            [
                'attrs.id'                 => 'PwaInstaller',
                'attrs.class'              => sprintf(
                    '%s hidden PwaInstaller--' . $this->get('style'),
                    $this->get('attrs.class')
                ),
                'attrs.data-pwa-installer' => 'banner',
            ]
        );

        $timeout = $this->get('timeout');
        if (is_numeric($timeout) && $timeout >= 1000) {
            $this->set('attrs.data-timeout', $timeout);
        }

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pwa()->resources('/views/partial/pwa-installer');
    }
}
