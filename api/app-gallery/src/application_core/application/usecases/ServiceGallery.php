<?php

namespace photopro\core\application\usecases;

use photopro\core\application\ports\api\GalleryDTO;
use photopro\core\application\ports\api\ServiceGalleryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;

class ServiceGallery implements ServiceGalleryInterface
{
    private GalleryRepositoryInterface $galleryRepository;

    public function __construct(GalleryRepositoryInterface $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    public function listOfGalery(): array
    {
        $galleries = $this->galleryRepository->findAll();

        return array_map(function ($gallery) {
            return new GalleryDTO(
                id: $gallery->id,
                photographerId: $gallery->photographerId,
                title: $gallery->title,
                description: $gallery->description,
                status: $gallery->status,
                type: $gallery->type,
                coverPhotoId: $gallery->coverPhotoId
            );
        }, $galleries);
    }
}