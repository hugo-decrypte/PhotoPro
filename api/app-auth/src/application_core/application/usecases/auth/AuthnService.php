<?php

namespace toubilib\core\application\usecases\auth;

use Ramsey\Uuid\Uuid;
use toubilib\api\dto\auth\AuthDTO;
use toubilib\api\dto\auth\CredentialsDTO;
use toubilib\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use toubilib\core\domain\entities\user\User;

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
        } catch (\toubilib\core\application\ports\spi\exceptions\EntityNotFoundException $e) {
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
            role: $user->role,
        );
    }

    public function createUser(CredentialsDTO $credentials, int $role): string
    {
        $uuid = Uuid::uuid4()->toString();
        $mdp = password_hash($credentials->getPassword(), PASSWORD_DEFAULT, ['cost' => 12]);
        $user = new User($uuid, $credentials->getEmail(), $mdp, $role);
        $this->authRepository->saveUser($user);
        return $uuid;
    }
}
