<?php
declare(strict_types=1);

use Slim\App;
use photopro\api\actions\PostAuthAction;
use photopro\api\actions\PostAuthNewUserAction;
use photopro\api\actions\PostAuthRefreshAction;
use photopro\api\actions\ValidateTokenAction;
use photopro\api\actions\GetUserAction;
use photopro\api\actions\PatchPasswordAction;

return function(App $app): App {
    $app->get('/users/{id}', GetUserAction::class )
        ->setName('user_id');

    //Authentification
    $app->post('/signin[/]', PostAuthAction::class )
        ->setName('auth_signin');

    $app->post('/refresh[/]', PostAuthRefreshAction::class)
        ->setName('auth_refresh');

    $app->post('/register[/]', PostAuthNewUserAction::class)
        ->setName('auth_signup');

    $app->post('/tokens/validate', ValidateTokenAction::class)
        ->setName('validate_token');

    $app->patch('/password[/]', PatchPasswordAction::class)
        ->setName('auth_password_patch');

    /**
     * CORS : options pour les requêtes preflight
     */
    $app->options('/{routes:.+}', function ($request, $response) {
        return $response;
    });

    return $app;
};