<?php

use DI\Container;
use photopro\core\application\ports\api\ServiceGalleryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use photopro\core\application\usecases\ServiceGallery;

return [
    ServiceGalleryInterface::class => function (Container $container) {
        $repository = $container->get(GalleryRepositoryInterface::class);
        return new ServiceGallery($repository);
    },
];
