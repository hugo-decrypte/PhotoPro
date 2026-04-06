<?php

namespace photopro\infra\adapter;

use photopro\core\application\ports\spi\repositoryInterfaces\ServiceAuthAdaptatorInterface;
use PDO;

class ServiceAuthAdaptor implements ServiceAuthAdaptatorInterface {
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getUserEmail(string $id): string
    {
        $stmt = $this->db->prepare("SELECT email FROM photographer WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }

        return $user['email'];
    }
}