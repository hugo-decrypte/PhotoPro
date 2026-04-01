<?php

namespace photo\core\application\ports\spi\repositoryInterfaces;

use photo\core\domain\entities\galery\Photo;

interface PhotoRepositoryInterface
{
    /**
     * Retourne une photo en fonction de son id
     * 
     * @return Photo
     */
    public function findOneById(int $id): Photo;

}
