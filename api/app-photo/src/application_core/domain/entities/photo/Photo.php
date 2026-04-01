<?php

namespace photo\core\domain\entities\galery;

use DateTime;
use GuzzleHttp\Psr7\MimeType;
use Ramsey\Uuid\UuidInterface;

class Photo
{
    public function __construct(
        private UuidInterface $id,
        private UuidInterface $photographerId,
        private MimeType $mimeType,
        private int $sizeBytes,
        private string $originalFilename,
        private String $s3Key,
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
            'id'               => $this->id,
//            'id'               => $this->id->toString(),
            'photographerId'   => $this->photographerId,
//            'photographerId'   => $this->photographerId->toString(),
            'mimeType'         => $this->mimeType,
//            'mimeType'         => $this->mimeType->value, // Ou ->toString() selon ton Enum/Objet
            'sizeBytes'        => $this->sizeBytes,
            'originalFilename' => $this->originalFilename,
            's3Key'            => $this->s3Key,
            'uploadedAt'       => $this->uploadedAt,
            'title'            => $this->title,
        ];
    }
}