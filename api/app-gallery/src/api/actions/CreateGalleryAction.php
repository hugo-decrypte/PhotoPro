<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalleryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateGalleryAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = (array) $request->getParsedBody();
            $photographerId = $request->getHeaderLine('X-Photographer-Id');

            if ($photographerId === '') {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            $gallery = $this->serviceGallery->createGallery($data, $photographerId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $gallery
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}