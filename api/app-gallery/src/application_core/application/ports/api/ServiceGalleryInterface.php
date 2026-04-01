<?php

namespace photopro\core\application\ports\api;

interface ServiceGalleryInterface
{
    public function listOfGalery(): array;

    public function createGallery(array $data, string $photographerId): array;

    public function accessPrivateGallery(string $galleryId, string $code): array;

    public function publishGallery(string $galleryId, string $photographerId): void;

    public function unpublishGallery(string $galleryId, string $photographerId): void;

    public function addComment(string $galleryId, string $photoId, array $data): array;

    public function listComments(string $galleryId): array;

}