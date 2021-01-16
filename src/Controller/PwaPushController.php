<?php

declare(strict_types=1);

namespace Pollen\Pwa\Controller;

use Throwable;
use League\Route\Http\Exception\ForbiddenException;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use tiFy\Contracts\Http\Response;
use tiFy\Contracts\View\Engine;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Url;
use tiFy\Support\Proxy\View;

class PwaPushController extends AbstractController
{
    /**
     * Racine de l'API
     *
     * @return array
     *
     * @throws ForbiddenException
     */
    public function api(): array
    {
        throw new ForbiddenException('Missing parameter');
    }

    /**
     * Ajout d'un abonné
     *
     * @return array
     */
    public function apiSubscribe(): array
    {
        return [
            'success' => false,
            'data'    => Request::all(),
        ];
    }

    /**
     * Test - Feuille de style CSS
     *
     * @return Response
     */
    public function testCss(): Response
    {
        $content = file_get_contents($this->pwa()->resources('assets/dist/css/push/push.test.styles.css'));

        return $this->response($content, 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Test - page HTML
     *
     * @return Response
     */
    public function testHtml(): Response
    {
        $this->set(
            'PushTest',
            [
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
        );
        return $this->view('test', $this->all());
    }

    /**
     * Test - Script JS
     *
     * @return Response
     */
    public function testJs(): Response
    {
        $content = file_get_contents($this->pwa()->resources('assets/dist/js/push/push.test.scripts.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * Test - Requête HTTP XHR de traitement de l'abonnement
     *
     * @return array
     *
     * @throws ForbiddenException
     */
    public function testSubscriptionXhr(): array
    {
        $subscription = Request::all();

        if (!isset($subscription['endpoint'])) {
            throw new ForbiddenException('Error: Push notifications subscription Invalid');
        }
        switch (Request::getMethod()) {
            case 'POST':
                return [
                    'Created: Push notifications subscription',
                ];
            case 'PUT':
                return [
                    'Updated: Push notifications subscription',
                ];
            case 'DELETE':
                return [
                    'Deleted: Push notifications subscription',
                ];
            default:
                throw new ForbiddenException('Error: Push notifications subscription Request method not handled');
        }
    }

    /**
     * Test - Requête HTTP XHR de traitement de l'envoi de message
     *
     * @return array
     *
     * @throws ForbiddenException
     */
    public function testSendXhr(): array
    {
        try {
            $subscription = $subscription = Subscription::create(Request::all());
        } catch (Throwable $e) {
            throw new ForbiddenException($e->getMessage());
        }
        try {
            $webPush = new WebPush(
                [
                    'VAPID' => [
                        'subject'    => Url::root('push-test.html')->render(),
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
            $reports[] = $webPush->sendOneNotification(
                $subscription,
                // @see https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification
                json_encode(
                    [
                        'title'              => 'Pwa Push Test',
                        'body'               => 'Hello World! 👋',
                        'requireInteraction' => false,
                        'vibrate'            => [300, 100, 400]
                    ]
                )
            );

            return [
                'success' => true,
                'data'    => $reports,
            ];
        } catch (Throwable $e) {
            throw new ForbiddenException($e->getMessage());
        }
    }

    /**
     * Test - Service Worker
     *
     * @return Response
     */
    public function testServiceWorker(): Response
    {
        $content = file_get_contents($this->pwa()->resources('assets/dist/js/push/push.test.service-worker.js'));

        return $this->response($content, 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * Moteur d'affichage des gabarits d'affichage.
     *
     * @return Engine
     */
    public function viewEngine(): Engine
    {
        return View::getPlatesEngine(
            [
                'directory' => $this->pwa()->resources('views/push'),
            ]
        );
    }
}
