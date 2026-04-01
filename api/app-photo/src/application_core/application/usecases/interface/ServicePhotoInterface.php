<?php

namespace photo\core\application\ports\api;

interface ServicePhotoInterface
{
    /**
     * Retourne une photo en fonction de son id
     * 
     * @return PhotoDTO
     */
    public function getPhoto(int $id): PhotoDTO;
}
