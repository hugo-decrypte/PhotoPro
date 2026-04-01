<?php

namespace toubilib\infra\repositories;

use auth\src\application_core\application\ports\spi\exceptions\DatabaseException;
use auth\src\application_core\application\ports\spi\exceptions\EntityNotFoundException;
use auth\src\application_core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use auth\src\application_core\domain\entities\user\User;

class PDOAuthRepository implements AuthRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws auth\src\application_core\application\ports\spi\exceptions\DatabaseException
     * @throws auth\src\application_core\application\ports\spi\exceptions\EntityNotFoundException
     */
    public function getByEmail($email): User
    {
        try {
            $stmt = $this->pdo->prepare('
            SELECT *
            FROM users 
            WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $userData = $stmt->fetch();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
        if(!$userData) {
            throw new EntityNotFoundException("User $email not found");
        }
        return User::fromArray($userData);
    }

    /**
     * @throws auth\src\application_core\application\ports\spi\exceptions\DatabaseException
     */
    public function saveUser(User $user): void
    {
        try {
            $stmt = $this->pdo->prepare('
            INSERT INTO users (id, email, password, role) 
            VALUES (:id, :email, :password, :role)');
            $stmt->execute(['id' => $user->id, 'email' => $user->email, 'password' => $user->password, 'role' => $user->role]);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @throws auth\src\application_core\application\ports\spi\exceptions\DatabaseException
     */
    public function deleteUser(User $user): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $user->id]);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
}