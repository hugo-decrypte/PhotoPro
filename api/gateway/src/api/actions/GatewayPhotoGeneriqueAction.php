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
        $id_photo = $args['id_photo'];
        return $this->transfererRequete($request, $response, "/photos/$id_photo");
    }

    public function uploadPhoto(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->transfererRequete($request, $response, "/photos");
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

        // Gestion du body et des fichiers
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'multipart/form-data')) {
            $multipart = [];
            // Ajouter les champs texte
            foreach ($request->getParsedBody() ?: [] as $name => $value) {
                $multipart[] = ['name' => $name, 'contents' => (string)$value];
            }
            // Ajouter les fichiers
            foreach ($request->getUploadedFiles() as $name => $file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $multipart[] = [
                        'name'     => $name,
                        'contents' => $file->getStream(),
                        'filename' => $file->getClientFilename()
                    ];
                }
            }
            $options['multipart'] = $multipart;
        } elseif (!empty($body) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if (is_array($body) || is_object($body)) {
                $options['json'] = $body;
            } else {
                $options['body'] = $body;
                $options['headers']['Content-Type'] = $contentType ?: 'application/json';
            }
        }

        try {
            $apiResponse = $this->httpClient->request($method, $path, $options);
            $data = $apiResponse->getBody()->getContents();
            $response->getBody()->write($data);

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
            return $response->withHeader('Content-Type', 'application/json')->withStatus(504);
        } catch (ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            $response->getBody()->write($errorBody ?: json_encode(['message' => 'Erreur service Photo (500)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
        }
    }
}
