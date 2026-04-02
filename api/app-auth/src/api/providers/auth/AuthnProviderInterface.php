<?php

namespace photopro\api\providers\auth;

use photopro\api\dto\auth\AuthDTO;
use photopro\api\dto\auth\CredentialsDTO;

interface AuthnProviderInterface
{
    public function register(CredentialsDTO $credentials, int $role=1): void;
    public function signin(CredentialsDTO $credentials): AuthDTO;
    public function refresh(AuthDTO $authDTO): AuthDTO;
    public function getSignedInUser(string $token): AuthDTO;
}