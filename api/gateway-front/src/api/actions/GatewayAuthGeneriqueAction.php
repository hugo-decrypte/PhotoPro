<?php

namespace photopro\api\actions;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayAuthGeneriqueAction
{
    private ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        return $this->transfererRequete($request, $response, '/register');
    }

    public function signin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        return $this->transfererRequete($request, $response, '/signin');
    }

    public function refresh(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        return $this->transfererRequete($request, $response, '/refresh');
    }

    public function validateToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/tokens/validate');
    }

    public function transfererRequete(ServerRequestInterface $request, ResponseInterface $response, string $path): ResponseInterface{
        $method = $request->getMethod();
        $body = $request->getParsedBody();

        // preparation des options de la requete
        $options = [];

        $authHeader = $request->getHeaderLine('Authorization');
        if (!empty($authHeader)) {
            $options['headers']['Authorization'] = $authHeader;
        }

        // Gestion du body si présent
        if (!empty($body) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if (is_array($body) || is_object($body)) {
                $options['json'] = $body;
            } else {
                $options['body'] = $body;
                $options['headers']['Content-Type'] =
                    $request->getHeaderLine('Content-Type') ?: 'application/json';
            }
        }

        try {
            // transfert de la requete vers l'API distante
            $apiResponse = $this->httpClient->request($method, $path, $options);

            $data = $apiResponse->getBody()->getContents();
            $response->getBody()->write($data);

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($apiResponse->getStatusCode());
            //Gestion des erreurs Guzzle
        } catch (ClientException $e) {
            // Erreurs 4xx
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();

            // Si l'API distante retourne déjà un JSON d'erreur, on le transmet
            $response->getBody()->write($errorBody ?: json_encode([
                'message' => 'Erreur lors de l\'appel au service distant'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($statusCode);

        } catch (ConnectException $e) {
            $response->getBody()->write(json_encode(['message' => 'Service Auth injoignable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(504);
        } catch (ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur service Auth (500)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
        }
    }
}