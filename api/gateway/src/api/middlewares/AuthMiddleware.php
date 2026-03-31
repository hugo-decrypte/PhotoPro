<?php

namespace toubilib\api\middlewares;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        //$authbody = $request->getParsedBody();



        if (empty($authHeader) || !preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            throw new HttpUnauthorizedException($request, 'Token d\'authentification manquant ou invalide');
        }

        $token = $matches[1];

        try {
            //requête à l'API auth pour valider le token
            $response = $this->client->request('POST', '/tokens/validate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpUnauthorizedException($request, 'Token d\'authentification invalide');
            }

            // Token valide
            $userData = json_decode($response->getBody()->getContents(), true);
            $body = $request->getParsedBody();
            $body['profile'] = $userData['user'];
            $request = $request->withParsedBody($body);
            return $handler->handle($request);

        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
            if ($statusCode === 401) {
                throw new HttpUnauthorizedException($request, 'Token d\'authentification invalide ou expiré');
            }

            // Autres erreurs
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'Erreur lors de la validation du token',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}