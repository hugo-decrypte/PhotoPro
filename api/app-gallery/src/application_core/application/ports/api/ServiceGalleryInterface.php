<?php

namespace photopro\core\application\ports\api;

interface ServiceGalleryInterface
{
    /**
     * Retourne la liste complète des praticiens avec leurs informations de base
     * 
     * @return GalleryDTO[]
     */
    public function listOfGalery(): array;
}
