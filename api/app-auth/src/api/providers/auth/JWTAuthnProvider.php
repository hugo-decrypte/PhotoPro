<?php

namespace photopro\api\providers\auth;

use photopro\api\dto\auth\AuthDTO;
use photopro\api\dto\auth\CredentialsDTO;
use photopro\api\providers\auth\AuthnProviderInterface;
use photopro\core\application\usecases\auth\AuthnServiceInterface;

class JWTAuthnProvider implements AuthnProviderInterface
{

    private AuthnServiceInterface $authnService;
    private JWTManager $jwtManager;

    public function __construct(AuthnServiceInterface $authnService)
    {
        $this->authnService = $authnService;
        $this->jwtManager = new JWTManager();
    }

    public function register(CredentialsDTO $credentials, int $role=1): void
    {
        $this->authnService->createUser($credentials, $role);
    }

    public function signin(CredentialsDTO $credentials): AuthDTO
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

    public function refresh(AuthDTO $authDTO): AuthDTO
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
    public function getSignedInUser(string $token): AuthDTO
    {
        try {
            $payload = $this->jwtManager->decodeToken($token);
        } catch (InvalidJWTTokenException $e) {
            throw new AuthnException($e->getMessage());
        }
        return new AuthDTO($payload['id'], $payload['email'], $payload['role']);
    }
}