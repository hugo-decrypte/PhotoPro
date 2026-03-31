<?php


use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
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


    /////////////////////////
    ///
    /// Remplacer toute cette partie par les services du nouveau projet
    ///
    /////////////////////////


//    'toubilib.praticiens.api' => 'http://api.praticien.toubilib/',
//    'toubilib.rdvs.api' => 'http://api.rdv.toubilib/',
//    'toubilib.patients.api' => 'http://api.patient.toubilib/',
//    'toubilib.auth.api' => 'http://api.auth.toubilib/',


//    //Application
//    'praticien.guzzle.client' => function (ContainerInterface $container) {
//        return new GuzzleHttp\Client([
//            'base_uri' => $container->get('toubilib.praticiens.api'),
//        ]);
//    },
//    'patient.guzzle.client' => function (ContainerInterface $container) {
//        return new GuzzleHttp\Client([
//            'base_uri' => $container->get('toubilib.patients.api'),
//        ]);
//    },
//    'rdv.guzzle.client' => function (ContainerInterface $container) {
//        return new GuzzleHttp\Client([
//            'base_uri' => $container->get('toubilib.rdvs.api'),
//        ]);
//    },
//
//    'auth.guzzle.client' => function (ContainerInterface $container) {
//        return new GuzzleHttp\Client([
//            'base_uri' => $container->get('toubilib.auth.api'),
//        ]);
//    },
//
//    GatewayPraticienGeneriqueAction::class => function (ContainerInterface $container) {
//        return new GatewayPraticienGeneriqueAction(
//            $container->get('praticien.guzzle.client')
//        );
//    },
//    GatewayPatientGeneriqueAction::class => function (ContainerInterface $container) {
//        return new GatewayPatientGeneriqueAction(
//            $container->get('patient.guzzle.client')
//        );
//    },
//    GatewayRdvGeneriqueAction::class => function (ContainerInterface $container) {
//        return new GatewayRdvGeneriqueAction(
//            $container->get('rdv.guzzle.client')
//        );
//    },
//
//    GatewayAuthGeneriqueAction::class => function (ContainerInterface $container) {
//        return new GatewayAuthGeneriqueAction(
//            $container->get('auth.guzzle.client')
//        );
//    },
//
//    AuthMiddleware::class => function (ContainerInterface $container) {
//        return new AuthMiddleware($container->get('auth.guzzle.client'));
//    },
//];

