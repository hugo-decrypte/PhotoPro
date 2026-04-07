<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetGalleryPhotosAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $galleryId = $args['id'];

            $photos = $this->serviceGallery->getPhotosByGalleryId($galleryId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $photos,
                'count' => count($photos)
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\RuntimeException $e) {
            $status = $e->getCode() ?: 400;

            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        }
    }
}
