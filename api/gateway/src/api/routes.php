<?php
declare(strict_types=1);

namespace toubilib\api;

use Slim\App;
use photopro\api\actions\GatewayAuthGeneriqueAction;
use photopro\api\actions\GatewayPhotoGeneriqueAction;
use photopro\api\actions\GatewayGalleryGeneriqueAction;

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

    // Route pour les gallery
    $app->get('/galeries', GatewayGalleryGeneriqueAction::class . ':getGallery')
        ->setName('api_gallery_get_gallery');

    // Routes pour les photos
    $app->get('/photos/{id_photo}', GatewayPhotoGeneriqueAction::class . ':getPhoto')
        ->setName('api_photo_get_photo');


    return $app;
};