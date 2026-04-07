<?php
declare(strict_types=1);

namespace photopro\api;

use Slim\App;
use photopro\api\actions\GatewayAuthGeneriqueAction;
use photopro\api\actions\GatewayPhotoGeneriqueAction;
use photopro\api\actions\GatewayGalleryGeneriqueAction;
use photopro\api\middlewares\AuthMiddleware;

return function(App $app): App {

    // (publiques)
    $app->post('/register', GatewayAuthGeneriqueAction::class . ':register');
    $app->post('/signin',   GatewayAuthGeneriqueAction::class . ':signin');
    $app->post('/refresh',  GatewayAuthGeneriqueAction::class . ':refresh');
    $app->post('/tokens/validate', GatewayAuthGeneriqueAction::class . ':validateToken');

    $app->get('/galeries', GatewayGalleryGeneriqueAction::class . ':getGalleries');
    $app->get('/galeries/{id}/privee', GatewayGalleryGeneriqueAction::class . ':getPrivateGallery');
    $app->get('/galeries/{id}/comments', GatewayGalleryGeneriqueAction::class . ':getComments');
    $app->get('/galeries/{id}/photos', GatewayGalleryGeneriqueAction::class . ':getGalleryPhotos');

    $app->get('/photos/{id_photo}', GatewayPhotoGeneriqueAction::class . ':getPhoto');


    // Gallery (protégées JWT)
    $app->group('', function ($group) {
        $group->post('/galeries/{id}/photos/{photoId}/comments', GatewayGalleryGeneriqueAction::class . ':addComment');
    })->add(AuthMiddleware::class);

    // CORS Catch-All
    $app->options('/{routes:.+}', function ($request, $response) {
        return $response;
    });

    return $app;
};