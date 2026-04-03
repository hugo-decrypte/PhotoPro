<?php

namespace photopro\api\actions;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayPhotoGeneriqueAction
{
    private ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getPhoto(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/photos/' . $args['id_photo']);
    }

    public function uploadPhoto(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Upload multipart : on transfère le body raw directement
        $contentType = $request->getHeaderLine('Content-Type');
        $options = [
            'headers' => [
                'Content-Type' => $contentType,
            ],
            'body' => $request->getBody(),
        ];

        $authHeader = $request->getHeaderLine('Authorization');
        if (!empty($authHeader)) {
            $options['headers']['Authorization'] = $authHeader;
        }

        try {
            $apiResponse = $this->httpClient->request('POST', '/photos', $options);
            $response->getBody()->write($apiResponse->getBody()->getContents());
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($apiResponse->getStatusCode());

        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur service Photo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);

        } catch (ConnectException $e) {
            $response->getBody()->write(json_encode(['message' => 'Service Photo injoignable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(503);

        } catch (ServerException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur interne service Photo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deletePhoto(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/photos/' . $args['id_photo']);
    }

    public function transfererRequete(ServerRequestInterface $request, ResponseInterface $response, string $path): ResponseInterface
    {
        $method = $request->getMethod();
        $body = $request->getParsedBody();
        $options = [];

        $authHeader = $request->getHeaderLine('Authorization');
        if (!empty($authHeader)) {
            $options['headers']['Authorization'] = $authHeader;
        }

        if (!empty($body) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if (is_array($body) || is_object($body)) {
                $options['json'] = $body;
            } else {
                $options['body'] = $body;
                $options['headers']['Content-Type'] = $request->getHeaderLine('Content-Type') ?: 'application/json';
            }
        }

        try {
            $apiResponse = $this->httpClient->request($method, $path, $options);
            $response->getBody()->write($apiResponse->getBody()->getContents());
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($apiResponse->getStatusCode());

        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur service Photo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);

        } catch (ConnectException $e) {
            $response->getBody()->write(json_encode(['message' => 'Service Photo injoignable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(503);

        } catch (ServerException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur interne service Photo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}