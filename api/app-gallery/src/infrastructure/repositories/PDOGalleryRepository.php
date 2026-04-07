<?php

namespace photopro\infra\repositories;

use PDO;
use photopro\core\application\ports\api\InputCommentDTO;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use photopro\core\domain\entities\galery\Gallery;
use Ramsey\Uuid\Uuid;

class PDOGalleryRepository implements GalleryRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return Gallery[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT 
                g.id,
                g.photographer_id,
                g.title,
                g.description,
                g.status,
                g.type,
                g.cover_photo_id
            FROM gallery g
            ORDER BY g.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $galleries = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $galleries[] = new Gallery(
                id: Uuid::fromString($row['id']),
                photographerId: Uuid::fromString($row['photographer_id']),
                title: $row['title'],
                description: $row['description'] ?? '',
                status: $row['status'] === 'PUBLISHED',
                type: $row['type'],
                coverPhotoId: $row['cover_photo_id']
                    ? Uuid::fromString($row['cover_photo_id'])
                    : null,
            );
        }

        return $galleries;
    }

    public function findByPhotographerId(string $idPhotographer): array{
        $sql = "
            SELECT 
                g.id,
                g.photographer_id,
                g.title,
                g.description,
                g.status,
                g.type,
                g.cover_photo_id
            FROM gallery g
            WHERE g.photographer_id = :idPhotographer
            ORDER BY g.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idPhotographer' => $idPhotographer]);

        $galleries = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $galleries[] = new Gallery(
                id: Uuid::fromString($row['id']),
                photographerId: Uuid::fromString($row['photographer_id']),
                title: $row['title'],
                description: $row['description'] ?? '',
                status: $row['status'] === 'PUBLISHED',
                type: $row['type'],
                coverPhotoId: $row['cover_photo_id']
                    ? Uuid::fromString($row['cover_photo_id'])
                    : null,
            );
        }
        return $galleries;
    }

    public function createGallery(array $galleryData, ?array $privateData = null): array
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO gallery (
                    id,
                    photographer_id,
                    title,
                    description,
                    status,
                    type,
                    cover_photo_id,
                    created_at,
                    published_at
                ) VALUES (
                    :id,
                    :photographer_id,
                    :title,
                    :description,
                    :status,
                    :type,
                    :cover_photo_id,
                    :created_at,
                    :published_at
                )
            ");
            $stmt->execute($galleryData);

            if ($privateData !== null) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO private_gallery_access (
                        gallery_id,
                        client_name,
                        client_email,
                        client_phone,
                        access_code,
                        direct_url
                    ) VALUES (
                        :gallery_id,
                        :client_name,
                        :client_email,
                        :client_phone,
                        :access_code,
                        :direct_url
                    )
                ");
                $stmt->execute($privateData);
            }

            $this->pdo->commit();

            return $this->findById($galleryData['id']);
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findById(string $galleryId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM gallery
            WHERE id = :id
        ");

        $stmt->execute(['id' => $galleryId]);

        $gallery = $stmt->fetch(PDO::FETCH_ASSOC);

        return $gallery ?: null;
    }

    public function findPrivateGalleryByCode(string $galleryId, string $code): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                g.id,
                g.photographer_id,
                g.title,
                g.description,
                g.status,
                g.type,
                g.cover_photo_id,
                g.created_at,
                g.published_at,
                pga.client_name,
                pga.client_email,
                pga.client_phone,
                pga.direct_url
            FROM gallery g
            INNER JOIN private_gallery_access pga ON pga.gallery_id = g.id
            WHERE g.id = :gallery_id
              AND g.type = 'PRIVATE'
              AND g.status = 'PUBLISHED'
              AND pga.access_code = :code
        ");

        $stmt->execute([
            'gallery_id' => $galleryId,
            'code' => $code,
        ]);

        $gallery = $stmt->fetch(PDO::FETCH_ASSOC);

        return $gallery ?: null;
    }

    public function publishGallery(string $galleryId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE gallery
            SET status = 'PUBLISHED',
                published_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute(['id' => $galleryId]);
    }

    public function unpublishGallery(string $galleryId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE gallery
            SET status = 'DRAFT',
                published_at = NULL
            WHERE id = :id
        ");

        $stmt->execute(['id' => $galleryId]);
    }

    public function countPhotos(string $galleryId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM gallery_photo
            WHERE gallery_id = :gallery_id
        ");

        $stmt->execute(['gallery_id' => $galleryId]);

        return (int) $stmt->fetchColumn();
    }

    public function addComment(InputCommentDTO $dto)
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO comment (
                id,
                photo_id,
                gallery_id,
                author_name,
                content,
                created_at
            ) VALUES (
                :id,
                :photo_id,
                :gallery_id,
                :author_name,
                :content,
                :created_at
            )
        ");

        $uuid = Uuid::uuid4()->toString();
        $stmt->execute([
            'id'           => $uuid,
            'photo_id'     => $dto->photoId,
            'gallery_id'   => $dto->galleryId,
            'author_name'  => $dto->authorName,
            'content'      => $dto->content,
            'created_at'   => $dto->createdAt->format('Y-m-d H:i:s')
        ]);
    }

    public function findCommentsByGalleryId(string $galleryId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                photo_id,
                gallery_id,
                author_name,
                content,
                created_at
            FROM comment
            WHERE gallery_id = :gallery_id
            ORDER BY created_at DESC
        ");

        $stmt->execute(['gallery_id' => $galleryId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPhotosToGallery(string $galleryId, array $photos): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO gallery_photo (gallery_id, photo_id, \"order\", added_at)
                VALUES (:gallery_id, :photo_id, :order, NOW())
                ON CONFLICT (gallery_id, photo_id) 
                DO UPDATE SET \"order\" = EXCLUDED.\"order\"
            ");

            foreach ($photos as $photo) {
                $stmt->execute([
                    'gallery_id' => $galleryId,
                    'photo_id' => $photo['photo_id'],
                    'order' => $photo['order'] ?? 0,
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}