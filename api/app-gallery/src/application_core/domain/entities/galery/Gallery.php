<?php

namespace photopro\core\domain\entities\galery;

use Ramsey\Uuid\UuidInterface;

class Gallery
{
    public function __construct(
        private UuidInterface $id,
        private UuidInterface $photographerId,
        private string $title,
        private string $description,
        private bool $status,
        private string $type = 'private',
        private UuidInterface $coverPhotoId,
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
            'id' => $this->id->toString(),
            'photographerId' => $this->photographerId->toString(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'type' => $this->type,
            'coverPhotoId' => $this->coverPhotoId->toString(),
        ];
    }
}