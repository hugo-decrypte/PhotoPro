<?php
declare(strict_types=1);

use photo\api\actions\UploadPhotoAction;
use photo\api\actions\DeletePhotoAction;
use photo\api\actions\PhotoAction;
use Slim\App;

return function( App $app): App {

    $app->get('/photos/{id_photo}', PhotoAction::class)->setName("photo.id");
    $app->post('/photos', UploadPhotoAction::class)->setName("upload.photo");
    $app->delete('/photos/{id_photo}', DeletePhotoAction::class)->setName("delete.photo.id");

    return $app;
};