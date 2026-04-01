<?php

use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use photopro\infra\repositories\PDOGalleryRepository;
use Psr\Container\ContainerInterface;

return [
    'db' => [
        'toubiprat' => [
            'driver' => 'pgsql',
            'host' => $_ENV['TOUBIPRAT_DB_HOST'] ?? 'photoproGallery.db',
            'port' => $_ENV['TOUBIPRAT_DB_PORT'] ?? 5432,
            'dbname' => $_ENV['TOUBIPRAT_DB_NAME'] ?? 'photoproGallery',
            'user' => $_ENV['TOUBIPRAT_DB_USER'] ?? 'photoproGallery',
            'password' => $_ENV['TOUBIPRAT_DB_PASS'] ?? 'photoproGallery',
        ]
    ],

    // Options PDO communs
    'pdo_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],

    // Connexion toubiprat
    'db.toubiprat' => function (ContainerInterface $c): PDO {
        $config = $c->get('db')['toubiprat'];
        $options = $c->get('pdo_options');
        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        return new PDO($dsn, $config['user'], $config['password'], $options);
    },

    // Repository Praticien
    GalleryRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOGalleryRepository($c->get('db.toubiprat'));
    },
];


