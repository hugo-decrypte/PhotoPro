<?php

use photopro\api\actions\ListeGalleryAction;
use photopro\api\actions\CreateGalleryAction;
use photopro\api\actions\AccessPrivateGalleryAction;
use photopro\api\actions\PublishGalleryAction;
use photopro\api\actions\UnpublishGalleryAction;
use photopro\api\actions\ListCommentsAction;
use photopro\api\actions\AddCommentAction;
use photopro\core\application\ports\api\ServiceGalleryInterface;


return [
    ListeGalleryAction::class => function ($c) {
        return new ListeGalleryAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },

    CreateGalleryAction::class => function ($c) {
        return new CreateGalleryAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },

    AccessPrivateGalleryAction::class => function ($c) {
        return new AccessPrivateGalleryAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },

    PublishGalleryAction::class => function ($c) {
        return new PublishGalleryAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },

    UnpublishGalleryAction::class => function ($c) {
        return new UnpublishGalleryAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },
    ListCommentsAction::class => function ($c) {
    return new ListCommentsAction(
        $c->get(ServiceGalleryInterface::class)
    );
    },

    AddCommentAction::class => function ($c) {
        return new AddCommentAction(
            $c->get(ServiceGalleryInterface::class)
        );
    },
];