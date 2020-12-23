<?php declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use tiFy\Partial\PartialDriver;
use Pollen\Pwa\PwaAwareTrait;

class InstallPromotionPartial extends PartialDriver
{
    use PwaAwareTrait;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        $this->set(
            'viewer.directory', $this->pwa()->resources()->path('views/partial/install-promotion')
        );
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            /**
             * @var string fixed|fixed-bottom
             */
            'style'   => 'fixed',
            'title'   => __('Installation', 'tify'),
            'content' => __('L\'installation n\'utilise quasiment pas de stockage et offre un moyen rapide et facile' .
                ' de revenir à cette application', 'tify')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set([
            'attrs.id'    => 'PwaInstallPromotion',
            'attrs.class' => sprintf(
                '%s hidden PwaInstallPromotion--' . $this->get('style'), $this->get('attrs.class', '')
            )
        ]);

        return parent::render();
    }
}