<?php
namespace photo\api\actions;

use photo\core\application\ports\api\ServicePhotoInterface;
use photo\core\services\StorageServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;

class UploadPhotoAction
{
    private ServicePhotoInterface $servicePhoto;
    private StorageServiceInterface $storageService;

    public function __construct(ServicePhotoInterface $servicePhoto, StorageServiceInterface $storageService)
    {
        $this->servicePhoto = $servicePhoto;
        $this->storageService = $storageService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $uploadedFiles = $request->getUploadedFiles();
        $file = $uploadedFiles['photo'] ?? null;

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(['error' => 'Aucun fichier valide reçu']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // 1. Stocker physiquement sur S3
            $stream = $file->getStream();
            $mime = $file->getClientMediaType();
            $originalName = $file->getClientFilename();
            $s3Key = $this->storageService->store($stream, $mime, 'uploads');

            // 2. Enregistrer en base de données SQL via le service métier
            $photoId = Uuid::uuid4()->toString();
            $parsedBody = $request->getParsedBody();
            $photographerId = $parsedBody['photographer_id'] ?? 'd975aca7-50c5-3d16-b211-cf7d302cba50';
            
            $this->servicePhoto->createPhoto(
                $photoId,
                $photographerId,
                $mime,
                $file->getSize(),
                $originalName,
                $s3Key,
                $parsedBody['title'] ?? $originalName
            );

            // 3. Réponse au client
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => [
                    'id' => $photoId,
                    's3_key' => $s3Key,
                    'original_filename' => $originalName,
                    'mime_type' => $mime,
                    'size' => $file->getSize()
                ]
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
