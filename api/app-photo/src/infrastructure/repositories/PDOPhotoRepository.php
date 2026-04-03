<?php

namespace photo\infra\repositories;

use DateTime;
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

    public function findOneById(string $id): Photo
    {
        $sql = "SELECT id, photographer_id, mime_type, size_bytes, original_filename, s3_key, uploaded_at, title FROM photo WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) throw new \Exception("Photo non trouvée pour l'id : $id");

        return new Photo(
            id: Uuid::fromString($res['id']),
            photographerId: Uuid::fromString($res['photographer_id']),
            mimeType: $res['mime_type'],
            sizeBytes: (int) $res['size_bytes'],
            originalFilename: $res['original_filename'],
            s3Key: $res['s3_key'],
            uploadedAt: new DateTime($res['uploaded_at']),
            title: $res['title'] ?? ''
        );
    }

    public function save(Photo $photo): void
    {
        $sql = "INSERT INTO photo (id, photographer_id, mime_type, size_bytes, original_filename, s3_key, uploaded_at, title) 
                VALUES (:id, :photographer_id, :mime_type, :size_bytes, :original_filename, :s3_key, :uploaded_at, :title)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $photo->id->toString(),
            'photographer_id' => $photo->photographerId->toString(),
            'mime_type' => $photo->mimeType,
            'size_bytes' => $photo->sizeBytes,
            'original_filename' => $photo->originalFilename,
            's3_key' => $photo->s3Key,
            'uploaded_at' => $photo->uploadedAt->format('Y-m-d H:i:s'),
            'title' => $photo->title ?? ''
        ]);
    }
}