<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UnpublishGalleryAction
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

            $this->serviceGallery->unpublishGallery($args['id'], $photographerId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Galerie dépubliée'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
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