<?php

namespace photopro\core\application\ports\api;

interface ServiceGalleryInterface
{
    public function listOfGalery(): array;
    public function listOfGalleyByPhotographer(string $idPhotographer): array;

    public function createGallery(array $data, string $photographerId): array;

    public function accessPrivateGallery(string $galleryId, string $code): array;

    public function publishGallery(string $galleryId, string $photographerId): void;

    public function unpublishGallery(string $galleryId, string $photographerId): void;

    public function addComment(InputCommentDTO $dto);

    public function listComments(string $galleryId): array;

    public function addPhotosToGallery(string $galleryId, array $photos, string $photographerId): void;

    public function getPhotosByGalleryId(string $galleryId): array;

}