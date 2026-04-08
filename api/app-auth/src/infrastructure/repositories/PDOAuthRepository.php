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
            SELECT id, email, password_hash as password, pseudo, first_name, name
            FROM photographer 
            WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
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
            INSERT INTO photographer (id, first_name, name, email, password_hash, pseudo) 
            VALUES (:id, :first_name, :name, :email, :password_hash, :pseudo)');
            $stmt->execute([
                'id' => $user->id, 
                'first_name' => $user->firstName ?: 'User', 
                'name' => $user->name ?: 'Photopro', 
                'email' => $user->email, 
                'password_hash' => $user->password, 
                'pseudo' => $user->pseudo ?: explode('@', $user->email)[0]
            ]);
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
            $stmt = $this->pdo->prepare('DELETE FROM photographer WHERE id = :id');
            $stmt->execute(['id' => $user->id]);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public function getById(string $id): User {
        try {
            $stmt = $this->pdo->prepare('
            SELECT id, email, password_hash as password, pseudo, first_name, name
            FROM photographer 
            WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
        if(!$userData) {
            throw new EntityNotFoundException("User $id not found");
        }
        return User::fromArray($userData);
    }

    public function updatePasswordHash(string $userId, string $passwordHash): void
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE photographer SET password_hash = :password_hash WHERE id = :id');
            $stmt->execute([
                'id' => $userId,
                'password_hash' => $passwordHash,
            ]);
            if ($stmt->rowCount() === 0) {
                throw new EntityNotFoundException("User $userId not found");
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
}