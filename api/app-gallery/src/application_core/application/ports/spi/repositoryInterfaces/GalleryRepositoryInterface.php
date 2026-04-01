<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\galery\Gallery;

interface GalleryRepositoryInterface
{
    /**
     * @return Gallery[]
     */
    public function findAll(): array;

    public function createGallery(array $galleryData, ?array $privateData = null): array;

    public function findById(string $galleryId): ?array;

    public function findPrivateGalleryByCode(string $galleryId, string $code): ?array;

    public function publishGallery(string $galleryId): void;

    public function unpublishGallery(string $galleryId): void;

    public function countPhotos(string $galleryId): int;
}