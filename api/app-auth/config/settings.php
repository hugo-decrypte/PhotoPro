<?php

use Psr\Container\ContainerInterface;

use toubilib\api\actions\PostAuthAction;
use toubilib\api\actions\PostAuthNewUserAction;
use toubilib\api\actions\PostAuthRefreshAction;
use toubilib\api\actions\ValidateTokenAction;
use toubilib\api\providers\auth\AuthnProviderInterface;
use toubilib\api\providers\auth\JWTAuthnProvider;
use toubilib\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use toubilib\core\application\usecases\auth\AuthnService;
use toubilib\core\application\usecases\auth\AuthnServiceInterface;
use toubilib\infra\repositories\PDOAuthRepository;

return [

    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'toubilib.db.config' => __DIR__ . '/.env',

    // application

    PostAuthAction::class => function (ContainerInterface $c) {
        return new PostAuthAction($c->get(AuthnProviderInterface::class));
    },
    PostAuthRefreshAction::class => function (ContainerInterface $c) {
        return new PostAuthRefreshAction($c->get(AuthnProviderInterface::class));
    },
    PostAuthNewUserAction::class => function (ContainerInterface $c) {
        return new PostAuthNewUserAction($c->get(AuthnProviderInterface::class));
    },

    ValidateTokenAction::class => function (ContainerInterface $c) {
        return new ValidateTokenAction($c->get(AuthnProviderInterface::class));
    },

    // service
    AuthnServiceInterface::class => function (ContainerInterface $c) {
        return new AuthnService($c->get(AuthRepositoryInterface::class));
    },
    AuthnProviderInterface::class => function (ContainerInterface $c) {
        return new JWTAuthnProvider($c->get(AuthnServiceInterface::class));
    },
    // infra

    'toubiauth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('toubilib.db.config'));
        $dsn = "{$config['auth.driver']}:host={$config['auth.host']};dbname={$config['auth.database']}";
        $user = $config['auth.username'];
        $password = $config['auth.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },

    AuthRepositoryInterface::class => fn (ContainerInterface $c) => new PDOAuthRepository($c->get('toubiauth.pdo')),

];

