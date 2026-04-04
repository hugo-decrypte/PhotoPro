<?php

namespace photopro\api\actions;

use photopro\core\application\usecases\auth\AuthnServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetUserAction {
    public function __construct(
        private AuthnServiceInterface $serviceAuth
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        if(empty($id)) {
            throw new \Exception("Saisissez un id d'utilisateur'");
        }

        try {
            $user = $this->serviceAuth->getUser($id);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => "Erreur lors de la récupération de l'utilisateur'",
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}

