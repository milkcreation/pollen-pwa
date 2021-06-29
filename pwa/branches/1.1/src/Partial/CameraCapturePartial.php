<?php

declare(strict_types=1);

namespace Pollen\Pwa\Partial;

use Pollen\Http\UrlHelper;

class CameraCapturePartial extends AbstractPwaPartial
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $urlHelper = new UrlHelper();

        $this->set(
            [
                'player' => [
                    'attrs' => [
                        'class'  => 'CameraCapture-player',
                        //'controls',
                        'autoplay',
                        'muted',
                        /*'poster' => $urlHelper->getAbsoluteUrl(
                            $this->pwa()->resources('/assets/src/img/photo-camera.png')
                        ),*/
                    ],
                    'tag'   => 'video',
                ],
            ]
        );

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pwa()->resources('/views/partial/camera-capture');
    }
}
