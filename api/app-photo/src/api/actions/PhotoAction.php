<?php

namespace photo\api\actions;

use photo\core\application\ports\api\ServicePhotoInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PhotoAction
{
    public function __construct(
        private ServicePhotoInterface $servicePhoto
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $id_photo = $args['id_photo'] ?? null;
        if(empty($id_photo)) {
            throw new \Exception("Saisissez un id de photo");
        }

        try {
            $photo = $this->servicePhoto->getPhoto($id_photo);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $photo
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Erreur lors de la récupération de la photo',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}

