<?php
declare(strict_types=1);

use photopro\api\actions\ListeGalleryAction;

return function( \Slim\App $app):\Slim\App {

    // Route pour lister tous les gallery
    $app->get('/praticiens', ListeGalleryAction::class)->setName("gallery.all");


    return $app;
};