<?php

namespace toubilib\api\providers\auth;

use auth\src\api\dto\auth\AuthDTO;
use auth\src\api\dto\auth\CredentialsDTO;
use auth\src\api\providers\auth\AuthnProviderInterface;
use auth\src\application_core\application\usecases\auth\AuthnServiceInterface;

class JWTAuthnProvider implements auth\src\api\providers\auth\AuthnProviderInterface
{

    private auth\src\application_core\application\usecases\auth\AuthnServiceInterface $authnService;
    private JWTManager $jwtManager;

    public function __construct(auth\src\application_core\application\usecases\auth\AuthnServiceInterface $authnService)
    {
        $this->authnService = $authnService;
        $this->jwtManager = new JWTManager();
    }

    public function register(auth\src\api\dto\auth\CredentialsDTO $credentials, int $role=1): void
    {
        $this->authnService->createUser($credentials, $role);
    }

    public function signin(auth\src\api\dto\auth\CredentialsDTO $credentials): auth\src\api\dto\auth\AuthDTO
    {
        $authDTO = $this->authnService->byCredentials($credentials);
        $authDTO->setRefreshToken($this->jwtManager->createRefreshToken(
            [
                'id' => $authDTO->getId(),
                'email' => $authDTO->getEmail(),
                'role' => $authDTO->getRole(),
            ]
        ));
        $authDTO->setAccessToken($this->jwtManager->createAccessToken(
            [
                'id' => $authDTO->getId(),
                'email' => $authDTO->getEmail(),
                'role' => $authDTO->getRole(),
            ]
        ));
        return $authDTO;
    }

    public function refresh(auth\src\api\dto\auth\AuthDTO $authDTO): auth\src\api\dto\auth\AuthDTO
    {
        $authDTO->setRefreshToken($this->jwtManager->createRefreshToken(
            [
                'id' => $authDTO->getId(),
                'email' => $authDTO->getEmail(),
                'role' => $authDTO->getRole(),
            ]
        ));
        $authDTO->setAccessToken($this->jwtManager->createAccessToken(
            [
                'id' => $authDTO->getId(),
                'email' => $authDTO->getEmail(),
                'role' => $authDTO->getRole(),
            ]
        ));
        return $authDTO;
    }

    /**
     * @throws AuthnException
     */
    public function getSignedInUser(string $token): auth\src\api\dto\auth\AuthDTO
    {
        try {
            $payload = $this->jwtManager->decodeToken($token);
        } catch (InvalidJWTTokenException $e) {
            throw new AuthnException($e->getMessage());
        }
        return new auth\src\api\dto\auth\AuthDTO($payload['id'], $payload['email'], $payload['role']);
    }
}