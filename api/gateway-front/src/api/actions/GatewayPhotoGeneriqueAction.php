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
        return $this->transfererRequete($request, $response, '/photos');
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

        // Gestion du body et des fichiers
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'multipart/form-data')) {
            $multipart = [];
            // Ajouter les champs texte
            foreach ($request->getParsedBody() ?: [] as $name => $value) {
                $this->appendMultipartField($multipart, (string)$name, $value);
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

    /**
     * Evite les warnings "Array to string conversion" sur payload multipart.
     */
    private function appendMultipartField(array &$multipart, string $name, mixed $value): void
    {
        if (is_array($value)) {
            foreach ($value as $k => $item) {
                $childName = is_int($k) ? "{$name}[]" : "{$name}[{$k}]";
                $this->appendMultipartField($multipart, $childName, $item);
            }
            return;
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif ($value === null) {
            $value = '';
        } elseif (is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE) ?: '';
        }

        $multipart[] = [
            'name' => $name,
            'contents' => (string)$value,
        ];
    }
}
