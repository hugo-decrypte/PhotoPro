<?php
namespace toubilib\infra;

use Aws\S3\S3Client;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\Uuid;
use toubilib\core\services\StorageServiceInterface;

class S3StorageService implements StorageServiceInterface
{
    private S3Client $s3Client;
    private string $bucket;

    public function __construct(S3Client $client, string $bucket)
    {
        $this->s3Client = $client;
        $this->bucket = $bucket;
    }

    /**
     * C'est le service applicatif qui génère les clés (UUID) !
     */
    public function store(StreamInterface $content, string $mimeType, string $prefix = ''): string
    {
        // Génération de l'UUID
        $uuid = Uuid::uuid4()->toString();
        $ext = $this->getExtensionFromMime($mimeType);
        
        // Organisation de la clé : "prefix/uuid.ext"
        $key = !empty($prefix) 
            ? rtrim($prefix, '/') . '/' . "{$uuid}.{$ext}" 
            : "{$uuid}.{$ext}";

        // Envoi au stockage (SeaweedFS)
        $this->s3Client->putObject([
            'Bucket'      => $this->bucket,
            'Key'         => $key,
            'Body'        => $content,
            'ContentType' => $mimeType,
            'ACL'         => 'private' // Toujours privé par défaut
        ]);

        return $key;
    }

    public function getPresignedUrl(string $key, string $expiration = '+20 minutes'): string
    {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $key
        ]);

        $request = $this->s3Client->createPresignedRequest($cmd, $expiration);
        return (string) $request->getUri();
    }

    public function delete(string $key): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $key
        ]);
    }

    private function getExtensionFromMime(string $mimeType): string
    {
        $mimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'application/pdf' => 'pdf'
        ];

        return $mimes[$mimeType] ?? 'bin';
    }
}
