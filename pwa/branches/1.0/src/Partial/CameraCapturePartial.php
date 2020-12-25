<?php declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use tiFy\Support\Proxy\Url;

class CameraCapturePartial extends AbstractPwaPartial
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set([
            'player' => [
                'attrs' => [
                    'class' => 'CameraCapture-player',
                    //'controls',
                    'autoplay',
                    'muted',
                    'poster' => Url::root($this->pwa()->resources()->rel('assets/src/img/photo-camera.png'))
                ],
                'tag' => 'video'
            ]
        ]);

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pwa()->resources()->path('views/partial/camera-capture');
    }
}
