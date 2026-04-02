<?php

use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use photopro\infra\repositories\PDOGalleryRepository;
use Psr\Container\ContainerInterface;

return [
    'db' => [
        'photopro_gallery' => [
            'driver' => 'pgsql',
            'host' => $_ENV['PHOTOPRO_GALLERY_DB_HOST'] ?? 'photopro-gallery.db',
            'port' => $_ENV['PHOTOPRO_GALLERY_DB_PORT'] ?? 5432,
            'dbname' => $_ENV['PHOTOPRO_GALLERY_DB_NAME'] ?? 'photopro_gallery',
            'user' => $_ENV['PHOTOPRO_GALLERY_DB_USER'] ?? 'photopro',
            'password' => $_ENV['PHOTOPRO_GALLERY_DB_PASS'] ?? 'photopro',
        ]
    ],

    'pdo_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],

    'db.photopro_gallery' => function (ContainerInterface $c): PDO {
        $config = $c->get('db')['photopro_gallery'];
        $options = $c->get('pdo_options');

        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        return new PDO($dsn, $config['user'], $config['password'], $options);
    },

    GalleryRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOGalleryRepository($c->get('db.photopro_gallery'));
    },
];