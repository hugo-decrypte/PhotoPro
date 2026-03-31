<?php
declare(strict_types=1);

namespace toubilib\api;

use Slim\App;
use toubilib\api\actions\GatewayAuthGeneriqueAction;
use toubilib\api\actions\GatewayPatientGeneriqueAction;
use toubilib\api\actions\GatewayPraticienGeneriqueAction;
use toubilib\api\actions\GatewayRdvGeneriqueAction;
use toubilib\api\middlewares\AuthMiddleware;

return function(App $app): App {

    /**
     * CORS : options pour les requêtes preflight
     */
    $app->options('/{routes:.+}', function ($request, $response) {
        return $response;
    });

    // Routes pour l'authentification (publiques)
    $app->post('/register', GatewayAuthGeneriqueAction::class . ':register')
        ->setName('api_auth_register');

    $app->post('/signin', GatewayAuthGeneriqueAction::class . ':signin')
        ->setName('api_auth_signin');

    $app->post('/refresh', GatewayAuthGeneriqueAction::class . ':refresh')
        ->setName('api_auth_refresh');

    $app->post('/tokens/validate', GatewayAuthGeneriqueAction::class . ':validateToken')
        ->setName('api_auth_validate_token');


    return $app;
};