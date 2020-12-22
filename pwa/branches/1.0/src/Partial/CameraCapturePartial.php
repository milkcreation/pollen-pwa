<?php declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use tiFy\Partial\PartialDriver;
use Pollen\Pwa\PwaAwareTrait;
use tiFy\Support\Proxy\Url;

class CameraCapturePartial extends PartialDriver
{
    use PwaAwareTrait;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        $this->set(
            'viewer.directory', $this->pwa()->resources()->path('views/partial/camera-capture')
        );
    }

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
                    'poster' => Url::root($this->pwa()->resources()->rel('assets/images/photo-camera.png'))
                ],
                'tag' => 'video'
            ]
        ]);

        return parent::render();
    }
}