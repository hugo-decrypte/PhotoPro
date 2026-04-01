<?php

namespace photopro\core\application\ports\api;

class GalleryDTO
{
    public function __construct(
        private readonly string $id,
        private readonly string $photographerId,
        private readonly string $title,
        private readonly string $description,
        private readonly bool $status,
        private readonly string $type,
        private readonly string $coverPhotoId,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'photographerId' => $this->photographerId,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'type' => $this->type,
            'coverPhotoId' => $this->coverPhotoId,
        ];
    }
}
