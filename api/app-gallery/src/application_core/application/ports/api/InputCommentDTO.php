<?php

namespace photopro\core\application\ports\api;

use DateTime;
use Exception;

class InputCommentDTO {
    private string $photoId;
    private string $galleryId;
    private ?string $authorName;
    private string $content;
    private DateTime $createdAt;

    /**
     * @param array $data
     * @throws Exception
     */
    public function __construct(array $data) {
        $this->photoId = $data['photoId'];
        $this->galleryId = $data['galleryId'];
        $this->authorName = $data['authorName'] ?? null;
        $this->content = $data['content'];
        $this->createdAt = new DateTime($data['createdAt']);
    }

    /**
     * @throws Exception
     */
    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new Exception("La propriété '$property' n'existe pas dans InputCommentDTO.");
    }
}