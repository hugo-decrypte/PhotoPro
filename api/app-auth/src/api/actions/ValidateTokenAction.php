<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use photopro\api\providers\auth\AuthnProviderInterface;

class ValidateTokenAction
{
    private AuthnProviderInterface $jwtProvider;

    public function __construct(AuthnProviderInterface $jwtProvider)
    {
        $this->jwtProvider = $jwtProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $auth);

        if (empty($token)) {
            throw new HttpUnauthorizedException($request, 'Token manquant');
        }

        try {
            // Récupérer les données de l'utilisateur depuis le token
            $userData = $this->jwtProvider->getSignedInUser($token);

            // Token valide - retourner le token et les données utilisateur
            $response->getBody()->write(json_encode([
                'message' => 'Token valide',
                'user' => [
                    'id' => $userData->getId(),
                    'email' => $userData->getEmail(),
                    'role' => $userData->getRole(),
                ]
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            // Token invalide
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }
}