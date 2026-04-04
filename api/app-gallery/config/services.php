<?php

use DI\Container;
use photopro\core\application\ports\api\ServiceGalleryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\MailSenderInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\ServiceAuthAdaptatorInterface;
use photopro\core\application\usecases\ServiceGallery;
use photopro\infra\adapter\ServiceAuthAdaptor;
use photopro\infra\repositories\MailSender;
use Psr\Container\ContainerInterface;

return [
    'auth.db.config' => __DIR__ . '/.auth.env',

    'auth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('auth.db.config'));
        $dsn = "{$config['auth.driver']}:host={$config['auth.host']};dbname={$config['auth.database']}";
        $user = $config['auth.username'];
        $password = $config['auth.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },
    ServiceGalleryInterface::class => function (Container $container) {
        return new ServiceGallery($container->get(GalleryRepositoryInterface::class),$container->get(ServiceAuthAdaptatorInterface::class),$container->get(MailSenderInterface::class));
    },

    ServiceAuthAdaptatorInterface::class => function (Container $c) {
        return new ServiceAuthAdaptor($c->get("auth.pdo"));
    },

    MailSenderInterface::class => function (ContainerInterface $c) {
        return new MailSender();
    },
];
