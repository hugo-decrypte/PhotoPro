<?php

namespace photo\core\application\usecases;

use photo\core\application\ports\api\ServicePhotoInterface;
use photo\core\application\ports\api\PhotoDTO;
use photo\core\application\ports\spi\repositoryInterfaces\PhotoRepositoryInterface;
use photo\core\domain\entities\galery\Photo;
use Ramsey\Uuid\Uuid;

class ServicePhoto implements ServicePhotoInterface
{
    private PhotoRepositoryInterface $photoRepository;

    public function __construct(PhotoRepositoryInterface $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    public function getPhoto(string $id): PhotoDTO
    {
        try {
            $photo = $this->photoRepository->findOneById($id);
            return new PhotoDTO($photo);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function createPhoto(string $id, string $photographerId, string $mimeType, int $sizeBytes, string $originalFilename, string $s3Key, string $title): void
    {
        try {
            $photo = new Photo(
                id: Uuid::fromString($id),
                photographerId: Uuid::fromString($photographerId),
                mimeType: $mimeType,
                sizeBytes: $sizeBytes,
                originalFilename: $originalFilename,
                s3Key: $s3Key,
                uploadedAt: new \DateTime(),
                title: $title
            );

            $this->photoRepository->save($photo);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function deletePhoto(string $id_photo) {
        try {
            $photo = $this->photoRepository->findOneById($id_photo);
            if (!$photo) {
                throw new \Exception("Photo $id_photo not found");
            }
            $this->photoRepository->deletePhoto($id_photo);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}