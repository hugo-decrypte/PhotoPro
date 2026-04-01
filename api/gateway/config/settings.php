<?php


use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Client;
use toubilib\api\actions\GatewayAuthGeneriqueAction;
use toubilib\api\actions\GatewayPatientGeneriqueAction;
use toubilib\api\actions\GatewayPraticienGeneriqueAction;
use toubilib\api\actions\GatewayRdvGeneriqueAction;
use toubilib\api\middlewares\AuthMiddleware;


return [

    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',

    'log.rdv.name' => 'rdv.log',
    'logger.rdv.file' => function (ContainerInterface $container) {
        $logger = $container->get('logs.dir') . DIRECTORY_SEPARATOR . 'log.rdv.name';
    },
    'logger.rdv.level' => \Monolog\Level::Info,


    // --------------------------------------------------------------------------
    // PhotoPro S3 Services
    // --------------------------------------------------------------------------
    's3.endpoint' => $_ENV['S3_ENDPOINT'] ?? 'http://seaweed-s3:8333',
    's3.region'   => $_ENV['S3_REGION']   ?? 'us-east-1',
    'S3_ACCESS_KEY' => $_ENV['S3_ACCESS_KEY'] ?? 'PHOTOPRO_ACCESS_KEY',
    'S3_SECRET_KEY' => $_ENV['S3_SECRET_KEY'] ?? 'PHOTOPRO_SECRET_KEY',
    's3.bucket'   => $_ENV['S3_BUCKET']     ?? 'photopro-photos',

    \Aws\S3\S3Client::class => function (ContainerInterface $c) {
        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $c->get('s3.region'),
            'endpoint' => $c->get('s3.endpoint'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $c->get('S3_ACCESS_KEY'),
                'secret' => $c->get('S3_SECRET_KEY'),
            ],
        ]);
    },

    \toubilib\core\services\StorageServiceInterface::class => function (ContainerInterface $c) {
        return new \toubilib\infra\S3StorageService(
            $c->get(\Aws\S3\S3Client::class),
            $c->get('s3.bucket')
        );
    },


    'toubilib.auth.api' => $_ENV['AUTH_API_URL'] ?? 'http://app-auth',


    'auth.guzzle.client' => function (ContainerInterface $container) {
        return new Client([
            'base_uri' => $container->get('toubilib.auth.api'),
            'timeout' => 10.0,
            'http_errors' => true,
        ]);
    },

    GatewayAuthGeneriqueAction::class => function (ContainerInterface $container) {
        return new GatewayAuthGeneriqueAction(
            $container->get('auth.guzzle.client')
        );
    },

    AuthMiddleware::class => function (ContainerInterface $container) {
        return new AuthMiddleware($container->get('auth.guzzle.client'));
    },
];

