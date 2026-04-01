<?php

use DI\Container;
use photopro\api\actions\ListeGalleryAction;
use photopro\core\application\ports\api\ServiceGalleryInterface;

return [
    // Liste des praticiens
    ListeGalleryAction::class => function ($c) {
        return new ListeGalleryAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },
];
