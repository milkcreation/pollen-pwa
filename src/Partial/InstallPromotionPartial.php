<?php

declare(strict_types=1);

namespace Pollen\Pwa\Partial;

class InstallPromotionPartial extends AbstractPwaPartial
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var string fixed|fixed-bottom
                 */
                'style'   => 'fixed',
                'title'   => __('Installation', 'pollen-pwa'),
                'content' => __(
                    'L\'installation n\'utilise quasiment pas de stockage et offre un moyen rapide et facile' .
                    ' de revenir à cette application',
                    'pollen-pwa'
                ),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set(
            [
                'attrs.id'    => 'PwaInstallPromotion',
                'attrs.class' => sprintf(
                    '%s hidden PwaInstallPromotion--' . $this->get('style'),
                    $this->get('attrs.class')
                ),
            ]
        );

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pwa()->resources('/views/partial/install-promotion');
    }
}
