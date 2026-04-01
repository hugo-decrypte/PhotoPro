<?php

namespace photo\core\application\usecases;

use photo\core\application\ports\api\PhotoDTO;
use photo\core\application\ports\api\ServicePhotoInterface;
use photo\core\application\ports\spi\repositoryInterfaces\PhotoRepositoryInterface;

class ServicePhoto implements ServicePhotoInterface
{
    private PhotoRepositoryInterface $photoRepository;

    public function __construct(PhotoRepositoryInterface $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    public function getPhoto(int $id): PhotoDTO
    {
        $photo = $this->photoRepository->findOneById($id);

        return new PhotoDTO(
            id: $photo->id,
            photographerId: $photo->photographerId,
//            id: $photo->id->toString(),
//            photographerId: $photo->photographerId->toString(),
            mimeType: $photo->mimeType,
//            mimeType: $photo->mimeType->value, // Ou ->toString() selon ta classe MimeType
            sizeBytes: $photo->sizeBytes,
            originalFilename: $photo->originalFilename,
            s3Key: $photo->s3Key,
            uploadedAt: $photo->uploadedAt,
//            uploadedAt: $photo->uploadedAt->format(DateTime::ATOM),
            title: $photo->title
        );
    }
}