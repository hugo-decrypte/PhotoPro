<?php

namespace photo\core\application\usecases;

use photo\core\application\ports\api\ServicePhotoInterface;
use photo\core\application\ports\api\PhotoDTO;
use photo\core\application\ports\spi\repositoryInterfaces\PhotoRepositoryInterface;

class ServicePhoto implements ServicePhotoInterface
{
    private PhotoRepositoryInterface $photoRepository;

    public function __construct(PhotoRepositoryInterface $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    public function getPhoto(string $id): PhotoDTO
    {
        $photo = $this->photoRepository->findOneById($id);
        return new PhotoDTO($photo);
    }

    public function createPhoto(string $id, string $photographerId, string $mimeType, int $sizeBytes, string $originalFilename, string $s3Key, string $title): void
    {
        $photo = new \photo\core\domain\entities\galery\Photo(
            id: \Ramsey\Uuid\Uuid::fromString($id),
            photographerId: \Ramsey\Uuid\Uuid::fromString($photographerId),
            mimeType: $mimeType,
            sizeBytes: $sizeBytes,
            originalFilename: $originalFilename,
            s3Key: $s3Key,
            uploadedAt: new \DateTime(),
            title: $title
        );

        $this->photoRepository->save($photo);
    }
}