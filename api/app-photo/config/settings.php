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
            'dbname' => $_ENV['PHOTO_DB_NAME'] ?? 'photopro_photo',
            'user' => $_ENV['PHOTO_DB_USER'] ?? 'photopro',
            'password' => $_ENV['PHOTO_DB_PASS'] ?? 'photopro',
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
    // Settings S3
    's3.endpoint' => $_ENV['S3_ENDPOINT'] ?? 'http://seaweed-s3:8333',
    's3.region'   => $_ENV['S3_REGION']   ?? 'us-east-1',
    'S3_ACCESS_KEY' => $_ENV['S3_ACCESS_KEY'] ?? 'PHOTOPRO_ACCESS_KEY',
    'S3_SECRET_KEY' => $_ENV['S3_SECRET_KEY'] ?? 'PHOTOPRO_SECRET_KEY',
    's3.bucket'   => $_ENV['S3_BUCKET']     ?? 'photopro-photos',

    // Client S3
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

    // Interface de stockage
    \photo\core\services\StorageServiceInterface::class => function (ContainerInterface $c) {
        return new \photo\infra\S3StorageService(
            $c->get(\Aws\S3\S3Client::class),
            $c->get('s3.bucket')
        );
    },

    PhotoRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPhotoRepository($c->get('db.photo'));
    },
];


