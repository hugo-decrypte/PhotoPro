<?php

namespace photopro\api\providers\auth;

use photopro\api\dto\auth\AuthDTO;
use photopro\api\dto\auth\CredentialsDTO;

interface AuthnProviderInterface
{
    public function register(CredentialsDTO $credentials): void;
    public function signin(CredentialsDTO $credentials): AuthDTO;
    public function refresh(AuthDTO $authDTO): AuthDTO;
    public function getSignedInUser(string $token): AuthDTO;
}