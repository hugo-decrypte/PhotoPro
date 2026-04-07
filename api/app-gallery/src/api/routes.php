<?php
declare(strict_types=1);

use photopro\api\actions\ListeGalleryAction;
use photopro\api\actions\CreateGalleryAction;
use photopro\api\actions\AccessPrivateGalleryAction;
use photopro\api\actions\PublishGalleryAction;
use photopro\api\actions\unpublishGalleryAction;
use photopro\api\actions\ListCommentsAction;
use photopro\api\actions\AddCommentAction;
use photopro\api\actions\AddPhotosToGalleryAction;
use photopro\api\actions\GetGalleryPhotosAction;
use photopro\api\middleware\CreateCommentValidationMiddleware;
use photopro\api\middleware\CreateGalleryValidationMiddleware;

return function( \Slim\App $app):\Slim\App {

    $app->get('/galeries', ListeGalleryAction::class)->setName('gallery.all');

    $app->post('/galeries', CreateGalleryAction::class)
        ->add(CreateGalleryValidationMiddleware::class)
        ->setName('gallery.create');

    $app->get('/galeries/{id}/privee', AccessPrivateGalleryAction::class)->setName('gallery.private.access');

    $app->patch('/galeries/{id}/publish', PublishGalleryAction::class)->setName('gallery.publish');

    $app->patch('/galeries/{id}/unpublish', UnpublishGalleryAction::class)->setName('gallery.unpublish');


    $app->get('/galeries/{id}/comments', ListCommentsAction::class)->setName('gallery.comments.list');
    $app->post('/galeries/{id}/photos/{photoId}/comments', AddCommentAction::class)
        ->add(CreateCommentValidationMiddleware::class)
        ->setName('gallery.comments.add');

    $app->get('/galeries/{id}/photos', GetGalleryPhotosAction::class)->setName('gallery.photos.list');
    $app->post('/galeries/{id}/photos', AddPhotosToGalleryAction::class)->setName('gallery.photos.add');

    return $app;
};