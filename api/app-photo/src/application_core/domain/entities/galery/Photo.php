<?php

namespace photo\core\domain\entities\galery;

use DateTime;
use Ramsey\Uuid\UuidInterface;

class Photo
{
    public function __construct(
        private UuidInterface $id,
        private UuidInterface $photographerId,
        private string $mimeType,
        private int $sizeBytes,
        private string $originalFilename,
        private string $s3Key,
        private DateTime $uploadedAt,
        private ?string $title = null
    ) {}

    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new \Exception("La propriété '$property' n'existe pas.");
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id->toString(),
            'photographerId'   => $this->photographerId->toString(),
            'mimeType'         => $this->mimeType,
            'sizeBytes'        => $this->sizeBytes,
            'originalFilename' => $this->originalFilename,
            's3Key'            => $this->s3Key,
            'uploadedAt'       => $this->uploadedAt->format('Y-m-d H:i:s'),
            'title'            => $this->title,
        ];
    }
}