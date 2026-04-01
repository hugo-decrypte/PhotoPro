<?php
namespace toubilib\core\services;

use Psr\Http\Message\StreamInterface;

interface StorageServiceInterface
{
    /**
     * Stocke un fichier et retourne sa clé S3
     * on génère l'UUID pour la clé.
     */
    public function store(StreamInterface $content, string $mimeType, string $prefix = ''): string;

    /**
     * Récupère une URL présignée temporaire pour accéder à l'objet
     */
    public function getPresignedUrl(string $key, string $expiration = '+20 minutes'): string;

    /**
     * Supprime un objet par sa clé
     */
    public function delete(string $key): void;
}
