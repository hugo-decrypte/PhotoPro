<?php

namespace photo\core\application\ports\api;

interface ServicePhotoInterface
{
    /**
     * Retourne une photo en fonction de son id
     * 
     * @return PhotoDTO
     */
    public function getPhoto(string $id): PhotoDTO;
    public function createPhoto(string $id, string $photographerId, string $mimeType, int $sizeBytes, string $originalFilename, string $s3Key, string $title): void;
}
