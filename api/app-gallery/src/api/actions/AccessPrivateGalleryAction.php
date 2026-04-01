<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccessPrivateGalleryAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $galleryId = $args['id'];
            $code = $request->getQueryParams()['code'] ?? '';

            if ($code === '') {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Code requis'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $gallery = $this->serviceGallery->accessPrivateGallery($galleryId, $code);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $gallery
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\RuntimeException $e) {
            $status = $e->getCode() ?: 403;

            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }
}