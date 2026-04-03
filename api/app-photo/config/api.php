<?php

use DI\Container;
use photo\api\actions\PhotoAction;
use photo\core\application\ports\api\ServicePhotoInterface;

return [
    // Liste des praticiens
    PhotoAction::class => function ($c) {
        return new PhotoAction(
            $c->get(ServicePhotoInterface::class)
        );
    },

    \photo\api\actions\UploadPhotoAction::class => function ($c) {
        return new \photo\api\actions\UploadPhotoAction(
            $c->get(ServicePhotoInterface::class),
            $c->get(\photo\core\services\StorageServiceInterface::class)
        );
    },
];
