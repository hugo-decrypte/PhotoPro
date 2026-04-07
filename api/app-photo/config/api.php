<?php

use photo\api\actions\DeletePhotoAction;
use photo\api\actions\PhotoAction;
use photo\api\actions\UploadPhotoAction;
use photo\core\application\ports\api\ServicePhotoInterface;
use photo\core\services\StorageServiceInterface;
use Psr\Container\ContainerInterface;

return [
    PhotoAction::class => function (ContainerInterface $c) {
        return new PhotoAction($c->get(ServicePhotoInterface::class));
    },
    DeletePhotoAction::class => function (ContainerInterface $c) {
        return new DeletePhotoAction($c->get(ServicePhotoInterface::class));
    },

    UploadPhotoAction::class => function ($c) {
        return new UploadPhotoAction(
            $c->get(ServicePhotoInterface::class),
            $c->get(StorageServiceInterface::class)
        );
    },
];
