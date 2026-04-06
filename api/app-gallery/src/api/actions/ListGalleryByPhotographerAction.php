<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListGalleryByPhotographerAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $photographerId = $args['photographerId'] ?? null;
            $authenticatedPhotographerId = $request->getHeaderLine('X-Photographer-Id');

            if (!$photographerId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'ID du photographe requis'
                ]));

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            if ($authenticatedPhotographerId && $authenticatedPhotographerId !== $photographerId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Vous ne pouvez accéder qu\'à vos propres galeries'
                ]));

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(403);
            }

            $galleries = $this->serviceGallery->listOfGalleyByPhotographer($photographerId);
            
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
                'error' => 'Erreur lors de la récupération des galeries du photographe',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
