<?php

namespace photopro\core\application\usecases;

use photopro\core\application\ports\api\GalleryDTO;
use photopro\core\application\ports\api\InputCommentDTO;
use photopro\core\application\ports\api\ServiceGalleryInterface;
use photopro\core\application\ports\spi\exceptions\EntityNotFoundException;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\MailSenderInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\ServiceAuthAdaptatorInterface;
use Ramsey\Uuid\Uuid;

class ServiceGallery implements ServiceGalleryInterface
{
    public function __construct(private GalleryRepositoryInterface $galleryRepository, private ServiceAuthAdaptatorInterface $adaptator, private MailSenderInterface $mail_sender) {}

    public function listOfGalery(): array
    {
        $galleries = $this->galleryRepository->findAll();

        return array_map(
            fn($gallery) => new GalleryDTO(
                id: $gallery->id->toString(),
                photographerId: $gallery->photographerId->toString(),
                title: $gallery->title,
                description: $gallery->description,
                status: $gallery->status,
                type: $gallery->type,
                coverPhotoId: $gallery->coverPhotoId?->toString()
            ),
            $galleries
        );
    }

    public function listOfGalleyByPhotographer(string $idPhotographer): array{
        try {
            $galleries = $this->galleryRepository->findByPhotographerId($idPhotographer);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return array_map(
            fn($gallery) => new GalleryDTO(
                id: $gallery->id->toString(),
                photographerId: $gallery->photographerId->toString(),
                title: $gallery->title,
                description: $gallery->description,
                status: $gallery->status,
                type: $gallery->type,
                coverPhotoId: $gallery->coverPhotoId?->toString()
            ),
            $galleries
        );
    }

    public function createGallery(array $data, string $photographerId): array
    {
        $title = trim((string)($data['title'] ?? ''));
        if ($title === '') {
            throw new \InvalidArgumentException('Titre obligatoire');
        }

        $type = strtoupper($data['type'] ?? 'PUBLIC');
        if (!in_array($type, ['PUBLIC', 'PRIVATE'], true)) {
            throw new \InvalidArgumentException('Type invalide');
        }

        $galleryId = Uuid::uuid4()->toString();

        $coverRaw = $data['cover_photo_id'] ?? null;
        $coverPhotoId = null;
        if ($coverRaw !== null && $coverRaw !== '') {
            $coverTrim = trim((string) $coverRaw);
            if ($coverTrim !== '') {
                $coverPhotoId = $coverTrim;
            }
        }

        $galleryData = [
            'id' => $galleryId,
            'photographer_id' => $photographerId,
            'title' => $title,
            'description' => $data['description'] ?? null,
            'status' => 'DRAFT',
            'type' => $type,
            'cover_photo_id' => $coverPhotoId,
            'created_at' => date('Y-m-d H:i:s'),
            'published_at' => null,
        ];

        $privateData = null;

        if ($type === 'PRIVATE') {
            $clientEmail = trim((string)($data['client_email'] ?? ''));
            if ($clientEmail === '') {
                throw new \InvalidArgumentException('Email client obligatoire pour une galerie privée');
            }

            $privateData = [
                'gallery_id' => $galleryId,
                'client_name' => $data['client_name'] ?? null,
                'client_email' => $clientEmail,
                'client_phone' => $data['client_phone'] ?? null,
                'access_code' => strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
                'direct_url' => '/galeries/' . $galleryId . '/privee',
            ];

            $this->mail_sender->send(
                $clientEmail,
                'Accès à votre galerie privée',
                'Votre galerie "' . $title . '" a été créée. Code d\'accès : ' . $privateData['access_code']
            );
        } else {
            $photographerEmail = $this->adaptator->getUserEmail($photographerId);
            $this->mail_sender->send(
                $photographerEmail,
                'Confirmation création de galerie',
                'La galerie "' . $title . '" a été créée avec succès.'
            );
        }

        return $this->galleryRepository->createGallery($galleryData, $privateData);
    }

    public function accessPrivateGallery(string $galleryId, string $code): array
    {
        $gallery = $this->galleryRepository->findPrivateGalleryByCode($galleryId, $code);

        if (!$gallery) {
            throw new \RuntimeException('Code invalide ou galerie privée introuvable', 403);
        }

        return $gallery;
    }

    public function publishGallery(string $galleryId, string $photographerId): void
    {
        $gallery = $this->galleryRepository->findById($galleryId);

        if (!$gallery) {
            throw new \RuntimeException('Galerie introuvable', 404);
        }

        if ($gallery['photographer_id'] !== $photographerId) {
            throw new \RuntimeException('Accès interdit', 403);
        }

        if ($this->galleryRepository->countPhotos($galleryId) < 1) {
            throw new \RuntimeException('Impossible de publier une galerie vide', 409);
        }

        $email = $this->adaptator->getUserEmail($photographerId);
        $this->mail_sender->send(
            $email,
            'Publication galerie',
            "La galerie " . $gallery['title'] . " vient d'être publiée."
        );

        $this->galleryRepository->publishGallery($galleryId);
    }

    public function unpublishGallery(string $galleryId, string $photographerId): void
    {
        $gallery = $this->galleryRepository->findById($galleryId);

        if (!$gallery) {
            throw new \RuntimeException('Galerie introuvable', 404);
        }

        if ($gallery['photographer_id'] !== $photographerId) {
            throw new \RuntimeException('Accès interdit', 403);
        }

        $email = $this->adaptator->getUserEmail($photographerId);
        $this->mail_sender->send(
            $email,
            'Dépublication galerie',
            "La galerie " . $gallery['title'] . " vient d'être dépubliée."
        );

        $this->galleryRepository->unpublishGallery($galleryId);
    }

    public function listComments(string $galleryId): array
    {
        $gallery = $this->galleryRepository->findById($galleryId);

        if (!$gallery) {
            throw new \RuntimeException('Galerie introuvable', 404);
        }

        return $this->galleryRepository->findCommentsByGalleryId($galleryId);
    }

    public function addComment(InputCommentDTO $dto)
    {
        $gallery = $this->galleryRepository->findById($dto->galleryId);
        if (!$gallery) {
            throw new \RuntimeException('Galerie introuvable', 404);
        }
        try {
            $this->galleryRepository->addComment($dto);

            $email = $this->adaptator->getUserEmail($gallery['photographer_id']);
            $this->mail_sender->send(
                $email,
                'Nouveau commentaire sur la galerie ' . $gallery['title'],
                "La galerie " . $gallery['title'] . " a reçu un nouveau commentaire de la part de " . $dto->authorName . '.'
            );
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e);
        } catch (\Error $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function addPhotosToGallery(string $galleryId, array $photos, string $photographerId): void
    {
        $gallery = $this->galleryRepository->findById($galleryId);

        if (!$gallery) {
            throw new \RuntimeException('Galerie introuvable', 404);
        }

        if ($gallery['photographer_id'] !== $photographerId) {
            throw new \RuntimeException('Accès interdit', 403);
        }

        if (empty($photos)) {
            throw new \InvalidArgumentException('Aucune photo à ajouter');
        }
        try {
            $this->galleryRepository->addPhotosToGallery($galleryId, $photos);
        } catch(\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getPhotosByGalleryId(string $galleryId): array
    {
        $gallery = $this->galleryRepository->findById($galleryId);

        if (!$gallery) {
            throw new \RuntimeException('Galerie introuvable', 404);
        }

        return $this->galleryRepository->findPhotosByGalleryId($galleryId);
    }
}