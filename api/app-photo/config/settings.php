<?php

use photo\core\application\ports\spi\repositoryInterfaces\PhotoRepositoryInterface;
use photo\infra\repositories\PDOPhotoRepository;
use Psr\Container\ContainerInterface;

return [
    'db' => [
        'photo' => [
            'driver' => 'pgsql',
            'host' => $_ENV['PHOTO_DB_HOST'] ?? 'photopro-photo.db',
            'port' => $_ENV['PHOTO_DB_PORT'] ?? 5432,
            'dbname' => $_ENV['PHOTO_DB_NAME'] ?? 'photoproPhoto',
            'user' => $_ENV['PHOTO_DB_USER'] ?? 'photoproPhoto',
            'password' => $_ENV['PHOTO_DB_PASS'] ?? 'photoproPhoto',
        ]
    ],

    // Options PDO communs
    'pdo_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],

    // Connexion toubiprat
    'db.photo' => function (ContainerInterface $c): PDO {
        $config = $c->get('db')['photo'];
        $options = $c->get('pdo_options');
        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        return new PDO($dsn, $config['user'], $config['password'], $options);
    },

    // Repository Praticien
    PhotoRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPhotoRepository($c->get('db.photo'));
    },
];


