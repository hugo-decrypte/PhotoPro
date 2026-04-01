<?php
declare(strict_types=1);

use auth\src\api\actions\PostAuthAction;
use auth\src\api\actions\PostAuthNewUserAction;
use auth\src\api\actions\PostAuthRefreshAction;
use auth\src\api\actions\ValidateTokenAction;
use auth\src\api\providers\auth\AuthnProviderInterface;
use Slim\App;
use toubilib\api\actions\DeleteAnnulerRDVAction;
use toubilib\api\actions\GetAllPraticienAction;
use toubilib\api\actions\GetPatientDetailAction;
use toubilib\api\actions\GetPraticienCreneauxAction;
use toubilib\api\actions\GetPraticienDetailAction;
use toubilib\api\actions\GetRDVDetailsAction;
use toubilib\api\actions\GetSearchRDVPatientAction;
use toubilib\api\actions\GetSearchRDVPraticienAction;
use toubilib\api\actions\PatchHonorerRDVAction;
use toubilib\api\actions\PostRDVCreerAction;
use toubilib\api\middlewares\AuthzRDVMiddleware;
use toubilib\api\middlewares\CheckNewRDV;
use toubilib\api\middlewares\CorsMiddleware;
use toubilib\core\application\usecases\authz\AuthzPatientService;
use toubilib\core\application\usecases\authz\AuthzPraticienService;
use toubilib\core\application\usecases\ServiceRDVInterface;

//use toubilib\api\middlewares\AuthnMiddleware;
//use toubilib\api\middlewares\AuthzPatientMiddleware;
//use toubilib\api\middlewares\AuthzPraticienMiddleware;

return function(App $app): App {

    //Authentification
    $app->post('/signin[/]', PostAuthAction::class )
        ->setName('auth_signin');

    $app->post('/refresh[/]', PostAuthRefreshAction::class)
        ->setName('auth_refresh');

    $app->post('/register[/]', PostAuthNewUserAction::class)
        ->setName('auth_signup');

    $app->post('/tokens/validate', ValidateTokenAction::class)
        ->setName('validate_token');
    /**
     * CORS : options pour les requêtes preflight
     */
    $app->options('/{routes:.+}', function ($request, $response) {
        return $response;
    });

    return $app;
};