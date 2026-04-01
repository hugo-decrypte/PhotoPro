<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\galery\Gallery;

interface GalleryRepositoryInterface
{
    /**
     * Retourne tous les praticiens avec leurs spécialités
     * 
     * @return Gallery[]
     */
    public function findAll(): array;

}
