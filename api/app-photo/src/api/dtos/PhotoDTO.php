<?php

namespace photo\core\application\ports\api;

use DateTime;
use GuzzleHttp\Psr7\MimeType;
use Ramsey\Uuid\UuidInterface;

class PhotoDTO
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly UuidInterface $photographerId,
        public readonly MimeType $mimeType,
        public readonly int $sizeBytes,
        public readonly string $originalFilename,
        public readonly string $s3Key,
        public readonly DateTime $uploadedAt,
        public readonly ?string $title = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'photographerId'   => $this->photographerId,
            'mimeType'         => $this->mimeType,
            'sizeBytes'        => $this->sizeBytes,
            'originalFilename' => $this->originalFilename,
            's3Key'            => $this->s3Key,
            'uploadedAt'       => $this->uploadedAt,
            'title'            => $this->title,
        ];
    }
}