<?php

namespace photopro\infra\adapter;

use PDO;
use photopro\core\application\ports\spi\repositoryInterfaces\ServiceAuthAdaptatorInterface;

class ServiceAuthAdaptor implements ServiceAuthAdaptatorInterface
{
    public function __construct(private PDO $pdo) {}

    public function getUserEmail(string $id): string
    {
        $stmt = $this->pdo->prepare("
            SELECT email
            FROM photographer
            WHERE id = :id
        ");

        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \RuntimeException("User with ID {$id} not found in auth service");
        }

        return $result['email'];
    }
}
