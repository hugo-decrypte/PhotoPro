<?php

namespace photo\infra\repositories;

use DateTime;
use GuzzleHttp\Psr7\MimeType;
use PDO;
use photo\core\domain\entities\galery\Photo;
use photo\core\application\ports\spi\repositoryInterfaces\PhotoRepositoryInterface;
use Ramsey\Uuid\Uuid;

class PDOPhotoRepository implements PhotoRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findOneById(int $id): Photo
    {
        $sql = "SELECT id, photographerId, mimeType, sizeBytes, originalFilename, s3Key, uploadedAt, title FROM photo WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(
            [
                "id" => $id
            ]
        );
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Photo(
            id: Uuid::fromString($res->id),
            photographerId: Uuid::fromString($res->photographerId),
            mimeType: MimeType::from($res->mimeType),
            sizeBytes: (int) $res->sizeBytes,
            originalFilename: $res->originalFilename,
            s3Key: $res->s3Key,
            uploadedAt: new DateTime($res->uploadedAt),
            title: $res->title
        );
    }
}