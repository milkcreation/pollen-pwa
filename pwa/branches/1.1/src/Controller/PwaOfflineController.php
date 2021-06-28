<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Pollen\Http\ResponseInterface;
use Pollen\Routing\BaseViewController;
use Pollen\Pwa\PwaInterface;
use Pollen\Pwa\PwaProxy;
use Pollen\Support\ProxyResolver;
use Pollen\View\ViewInterface;
use Pollen\View\ViewManager;
use Pollen\View\ViewManagerInterface;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;

class PwaOfflineController extends BaseViewController
{
    use PwaProxy;

    /**
     * @param PwaInterface $pwa
     * @param Container|null $container
     */
    public function __construct(PwaInterface $pwa, ?Container $container = null)
    {
        $this->setPwa($pwa);

        parent::__construct($container);
    }

    /**
     * Moteur d'affichage des gabarits d'affichage.
     *
     * @return ViewInterface
     */
    protected function getView(): ViewInterface
    {
        if ($this->view === null) {
            try {
                $manager = ViewManager::getInstance();
            } catch (RuntimeException $e) {
                $manager = ProxyResolver::getInstance(
                    ViewManagerInterface::class,
                    ViewManager::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }

            $this->view = $manager->createView('plates')
                ->setDirectory($this->pwa()->resources('/views/offline'));
        }

        return $this->view;
    }

    /**
     * Page Html
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $this->datas(
            [
                'css' => $this->css()->getContent(),
                'js'  => $this->js()->getContent(),
            ]
        );

        return $this->cachedResponse($this->view('index'));
    }

    /**
     * Css
     *
     * @return ResponseInterface
     */
    public function css(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/css/offline.css'));

        $response = $this->response($content, 200, ['Content-Type' => 'text/css']);

        return $this->cachedResponse($response);
    }

    /**
     * Js
     *
     * @return ResponseInterface
     */
    public function js(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/offline.js'));

        $response = $this->response($content, 200, ['Content-Type' => 'text/javascript']);

        return $this->cachedResponse($response);
    }
}
