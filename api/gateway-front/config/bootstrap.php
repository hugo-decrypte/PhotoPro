<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use photopro\api\middlewares\CorsMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();

$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/settings.php');
$c=$builder->build();
$app = AppFactory::createFromContainer($c);

$app->addBodyParsingMiddleware();

$app->add(new CorsMiddleware(
    allowedOrigins: $_ENV['CORS_ALLOWED_ORIGINS'] ? explode(',', $_ENV['CORS_ALLOWED_ORIGINS']) : ['http://localhost:3000', 'http://localhost:5173'], // Origines par défaut pour dev
    allowedMethods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
    allowCredentials: true,
));


$app->addRoutingMiddleware();
$app->addErrorMiddleware($c->get('displayErrorDetails'), false, false)
    ->getDefaultErrorHandler()
    ->forceContentType('application/json')
;

$app = (require_once __DIR__ . '/../src/api/routes.php')($app);


return $app;


