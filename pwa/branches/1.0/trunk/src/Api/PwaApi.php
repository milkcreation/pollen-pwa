<?php declare(strict_types=1);

namespace Pollen\Pwa\Api;

use tiFy\Contracts\Routing\RouteGroup;
use tiFy\Support\Proxy\{Request, Router};

class PwaApi
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __invoke()
    {
        Router::group('/pwa/api', function(RouteGroup $router) {
            $router->map('GET', '/', [$this, 'root']);
            $router->map('GET', '/subscriber', [$this, 'addSubscriber']);
        })->strategy('json');
    }

    /**
     *
     * @return array
     */
    public function root()
    {
        return ['error' => 'Paramètres manquant'];
    }

    /**
     *
     * @return array
     */
    public function addSubscriber()
    {
        return Request::all();
    }
}