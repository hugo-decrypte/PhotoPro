<?php

namespace photopro\api\actions;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayGalleryGeneriqueAction
{
    private ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getGalleries(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries');
    }

    public function getGalleriesByPhotographer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/photographer/' . $args['photographerId']);
    }

    public function createGallery(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries');
    }

    public function getPrivateGallery(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $queryString = $request->getUri()->getQuery();
        $path = '/galeries/' . $args['id'] . '/privee';
        if ($queryString) {
            $path .= '?' . $queryString;
        }
        return $this->transfererRequete($request, $response, $path);
    }

    public function publishGallery(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/' . $args['id'] . '/publish');
    }

    public function unpublishGallery(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/' . $args['id'] . '/unpublish');
    }

    public function getComments(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/' . $args['id'] . '/comments');
    }

    public function addComment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/' . $args['id'] . '/photos/' . $args['photoId'] . '/comments');
    }

    public function addPhotosToGallery(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/' . $args['id'] . '/photos');
    }

    public function getGalleryPhotos(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, '/galeries/' . $args['id'] . '/photos');
    }

    public function transfererRequete(ServerRequestInterface $request, ResponseInterface $response, string $path): ResponseInterface
    {
        $method = $request->getMethod();
        $body = $request->getParsedBody();

        // preparation des options de la requete
        $options = [];

        $authHeader = $request->getHeaderLine('Authorization');
        if (!empty($authHeader)) {
            $options['headers']['Authorization'] = $authHeader;
        }

        $photographerHeader = $request->getHeaderLine('X-Photographer-Id');
        if (!empty($photographerHeader)) {
            $options['headers']['X-Photographer-Id'] = $photographerHeader;
        }

        // Query parameters
        $queryParams = $request->getQueryParams();
        if (!empty($queryParams)) {
            $options['query'] = $queryParams;
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
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur service Gallery']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);

        } catch (ConnectException $e) {
            $response->getBody()->write(json_encode(['message' => 'Service Gallery injoignable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(503);

        } catch (ServerException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur interne service Gallery']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}