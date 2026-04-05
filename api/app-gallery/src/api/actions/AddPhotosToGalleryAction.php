<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddPhotosToGalleryAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $photographerId = $request->getHeaderLine('X-Photographer-Id');

            if ($photographerId === '') {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            $galleryId = $args['id'];
            $data = (array) $request->getParsedBody();
            $photos = $data['photos'] ?? [];

            if (!is_array($photos)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Le champ "photos" doit être un tableau'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $this->serviceGallery->addPhotosToGallery($galleryId, $photos, $photographerId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => count($photos) . ' photo(s) ajoutée(s) à la galerie'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\RuntimeException $e) {
            $status = $e->getCode() ?: 400;

            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }
}
