<?php

namespace photopro\core\application\usecases\auth;

use photopro\api\dto\auth\AuthDTO;
use photopro\api\dto\auth\CredentialsDTO;
use photopro\api\dto\auth\UserDTO;
use photopro\core\application\ports\spi\exceptions\EntityNotFoundException;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\core\domain\entities\user\User;
use Ramsey\Uuid\Uuid;

class AuthnService implements AuthnServiceInterface
{
    public AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function byCredentials(CredentialsDTO $credentials): AuthDTO
    {
        // Vérification des champs
        if (empty($credentials->getEmail()) || empty($credentials->getPassword())) {
            throw new \InvalidArgumentException("Email et mot de passe sont requis.");
        }

        // Recherche de l'utilisateur par email
        try {
            $user = $this->authRepository->getByEmail($credentials->getEmail());
        } catch (EntityNotFoundException $e) {
            throw new \RuntimeException("Utilisateur introuvable pour l'email fourni.");
        }

        // Vérification du mot de passe
        if(!password_verify($credentials->getPassword(), $user->password)){
            throw new \RuntimeException("Mot de passe incorrect.");
        }


        // Création du DTO d’authentification
        return new AuthDTO(
            id: $user->id,
            email: $user->email,
        );
    }

    public function createUser(CredentialsDTO $credentials): string
    {
        $uuid = Uuid::uuid4()->toString();
        $mdp = password_hash($credentials->getPassword(), PASSWORD_DEFAULT, ['cost' => 12]);
        $user = new User($uuid, $credentials->getEmail(), $mdp);
        $user->firstName = $credentials->getFirstName();
        $user->name = $credentials->getName();
        $user->pseudo = $credentials->getPseudo();
        $this->authRepository->saveUser($user);
        return $uuid;
    }

    public function getUser(string $id): UserDTO {
        try {
            $user = $this->authRepository->getById($id);

            return new UserDTO(
                id: $user->id,
                email: $user->email,
                password: $user->password
            );
        } catch (EntityNotFoundException $e) {
            throw new \RuntimeException("Utilisateur introuvable pour l'email fourni.");
        }
    }
}
