<?php

namespace photo\core\application\ports\api;

use photo\core\domain\entities\galery\Photo;
use JsonSerializable;

class PhotoDTO implements JsonSerializable
{
    private array $data;

    public function __construct(Photo $photo)
    {
        $this->data = $photo->toArray();
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}