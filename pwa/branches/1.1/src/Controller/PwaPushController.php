<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use League\Route\Http\Exception\ForbiddenException;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Pollen\Http\UrlHelper;
use Pollen\Http\ResponseInterface;
use Pollen\Http\JsonResponseInterface;
use Pollen\Routing\BaseViewController;
use Pollen\Pwa\PwaInterface;
use Pollen\Pwa\PwaProxy;
use Psr\Container\ContainerInterface as Container;
use Throwable;

class PwaPushController extends BaseViewController
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
     * Racine de l'API
     *
     * @return JsonResponseInterface
     *
     * @throws ForbiddenException
     */
    public function api(): JsonResponseInterface
    {
        throw new ForbiddenException('Missing parameter');
    }

    /**
     * Ajout d'un abonné
     *
     * @return JsonResponseInterface
     */
    public function apiSubscribe(): JsonResponseInterface
    {
        return $this->json([
            'success' => false,
            'data'    => $this->httpRequest()->request->all(),
        ]);
    }

    /**
     * Test - Feuille de style CSS
     *
     * @return ResponseInterface
     */
    public function testCss(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/css/push/push.test.styles.css'));

        return $this->response($content, 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Test - page HTML
     *
     * @return ResponseInterface
     */
    public function testHtml(): ResponseInterface
    {
        $this->params([
            'PushTest' => [
                'l10n'       => [
                    'button_default'  => __('Activer/Désactiver', 'pollen-pwa'),
                    'sending'         => __('Envoyer', 'pollen-pwa'),
                    'enabled'         => __('Désactiver', 'pollen-pwa'),
                    'disabled'        => __('Activer', 'pollen-pwa'),
                    'computing'       => __('Chargement...', 'pollen-pwa'),
                    'incompatible'    => __('Indisponible depuis ce navigateur', 'pollen-pwa'),
                    'please_enabling' => __('Veuillez d\'abord activer les notifications !', 'pollen-pwa'),
                ],
                'public_key' => 'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0',
            ]
        ]);
        return $this->view('test', $this->params()->all());
    }

    /**
     * Test - Script JS
     *
     * @return ResponseInterface
     */
    public function testJs(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/push/push.test.scripts.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * Test - Requête HTTP XHR de traitement de l'abonnement
     *
     * @return JsonResponseInterface
     *
     * @throws ForbiddenException
     */
    public function testSubscriptionXhr(): JsonResponseInterface
    {
        if (!$this->httpRequest()->isMethod('POST')) {
            try {
                $subscription = json_decode($this->httpRequest()->getContent(), true, 512, JSON_THROW_ON_ERROR);

                if (!isset($subscription['endpoint'])) {
                    throw new ForbiddenException(
                        'Error: Push notifications subscription Invalid >> Endpoint Subscription missing'
                    );
                }

            } catch (Throwable $e) {
                throw new ForbiddenException('Error: Push notifications subscription Invalid');
            }
        }

        switch ($this->httpRequest()->getMethod()) {
            case 'POST':
                return $this->json([
                    'Created: Push notifications subscription',
                ]);
            case 'PUT':
                return $this->json([
                    'Updated: Push notifications subscription',
                ]);
            case 'DELETE':
                return $this->json([
                    'Deleted: Push notifications subscription',
                ]);
            default:
                throw new ForbiddenException('Error: Push notifications subscription Request method not handled');
        }
    }

    /**
     * Test - Requête HTTP XHR de traitement de l'envoi de message
     *
     * @return JsonResponseInterface
     *
     * @throws ForbiddenException
     */
    public function testSendXhr(): JsonResponseInterface
    {
        try {
            $datas = json_decode($this->httpRequest()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $subscription = Subscription::create($datas);
        } catch (Throwable $e) {
            throw new ForbiddenException($e->getMessage());
        }
        try {
            $urlHelper = new UrlHelper($this->httpRequest());

            $webPush = new WebPush(
                [
                    'VAPID' => [
                        'subject'    => $urlHelper->getAbsoluteUrl('push-test.html'),
                        'publicKey'  => file_get_contents($this->pwa()->resources('/keys/push.test.public_key.txt')),
                        'privateKey' => file_get_contents($this->pwa()->resources('/keys/push.test.private_key.txt')),
                    ],
                ]
            );
        } catch (Throwable $e) {
            throw new ForbiddenException($e->getMessage());
        }
        $reports = [];
        try {
            // @see https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification
            $params = json_encode(
                [
                    'title'              => 'Pwa Push Test',
                    'body'               => 'Hello World! 👋',
                    'requireInteraction' => false,
                    'vibrate'            => [300, 100, 400]
                ],
                JSON_THROW_ON_ERROR
            );

            $reports[] = $webPush->sendOneNotification($subscription, $params);

            return $this->json([
                'success' => true,
                'data'    => $reports,
            ]);
        } catch (Throwable $e) {
            throw new ForbiddenException($e->getMessage());
        }
    }

    /**
     * Test - Service Worker
     *
     * @return ResponseInterface
     */
    public function testServiceWorker(): ResponseInterface
    {
        $content = file_get_contents($this->pwa()->resources('/assets/dist/js/push/push.test.service-worker.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * @inheritDoc
     */
    public function viewEngineDirectory(): string
    {
        return $this->pwa()->resources('/views/push');
    }
}
