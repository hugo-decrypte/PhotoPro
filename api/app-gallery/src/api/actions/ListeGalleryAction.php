<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListeGalleryAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {

            $galleries = $this->serviceGallery->listOfGalery();
            
            $data = array_map(function ($gallery) {
                return $gallery->toArray();
            }, $galleries);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $data,
                'count' => count($data)
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Erreur lors de la récupération des gallery',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}

