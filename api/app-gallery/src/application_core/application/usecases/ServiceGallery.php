<?php

namespace photopro\core\application\usecases;

use photopro\core\application\ports\api\GalleryDTO;
use photopro\core\application\ports\api\ServiceGalleryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\GalleryRepositoryInterface;

class ServiceGallery implements ServiceGalleryInterface
{
    private PraticienRepositoryInterface $praticienRepository;
    private GalleryRepositoryInterface $galleryRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository, GalleryRepositoryInterface $galleryRepository)
    {
        $this->praticienRepository = $praticienRepository;
        $this->galleryRepository = $galleryRepository;
    }

    public function listerPraticiens(): array
    {
        $praticiens = $this->praticienRepository->findAll();
        
        return array_map(function ($praticien) {
            return new PraticienDTO(
                id: $praticien->getId()->toString(),
                nom: $praticien->getNom(),
                prenom: $praticien->getPrenom(),
                ville: $praticien->getVille(),
                email: $praticien->getEmail(),
                specialite: $praticien->getSpecialiteLibelle() // Cette méthode sera ajoutée à l'entité
            );
        }, $praticiens);
    }

    public function listOfGalery(): array
    {
        $galleries = $this->galleryRepository->findAll();

        return array_map(function ($gallery) {
            return new GalleryDTO(
                id: $gallery->id,
                photographerId: $gallery->photographerId,
                title: $gallery->title,
                description: $gallery->description,
                status: $gallery->status,
                type: $gallery->type,
                coverPhotoId: $gallery->coverPhotoId
            );
        }, $galleries);
    }
}