<?php
declare(strict_types=1);

use photo\api\actions\PhotoAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/photos/{id_photo}', PhotoAction::class)->setName("photo.id");
    $app->post('/photos', \photo\api\actions\UploadPhotoAction::class)->setName("photo.upload");

    return $app;
};