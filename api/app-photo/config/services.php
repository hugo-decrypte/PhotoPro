<?php

use DI\Container;
use photo\core\application\ports\api\ServicePhotoInterface;
use photo\core\application\ports\spi\repositoryInterfaces\PhotoRepositoryInterface;
use photo\core\application\usecases\ServicePhoto;

return [
    ServicePhotoInterface::class => function (Container $container) {
        $repository = $container->get(PhotoRepositoryInterface::class);
        return new ServicePhoto($repository);
    },
];
