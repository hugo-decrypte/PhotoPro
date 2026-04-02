<?php

namespace photopro\infra\repositories;

use photopro\core\application\ports\spi\exceptions\DatabaseException;
use photopro\core\application\ports\spi\exceptions\EntityNotFoundException;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\core\domain\entities\user\User;

class PDOAuthRepository implements AuthRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws DatabaseException
     * @throws EntityNotFoundException
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
     * @throws DatabaseException
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
     * @throws DatabaseException
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