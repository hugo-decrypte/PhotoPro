<?php

namespace photopro\infra\repositories;

use PDO;
use photopro\core\domain\entities\galery\Gallery;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;
use Ramsey\Uuid\Uuid;

class PDOGalleryRepository implements GalleryRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT 
                g.id,
                g.photographerId,
                g.title,
                g.description,
                g.status,
                g.type,
                g.coverPhotoId,
            FROM galery g
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $galery = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $galery[] = new Gallery(
                id: Uuid::fromString($row['id']),
                photographerId: $row['photographerId'],
                title: $row['title'],
                description: $row['description'],
                status: $row['status'],
                type: $row['type'],
                coverPhotoId: $row['coverPhotoId'],
            );
        }

        return $galery;
    }
}