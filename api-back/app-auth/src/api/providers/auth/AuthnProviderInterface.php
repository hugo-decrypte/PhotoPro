<?php

namespace toubilib\api\providers\auth;

use auth\src\api\dto\auth\AuthDTO;
use auth\src\api\dto\auth\CredentialsDTO;

interface AuthnProviderInterface
{
    public function register(auth\src\api\dto\auth\CredentialsDTO $credentials, int $role=1): void;
    public function signin(auth\src\api\dto\auth\CredentialsDTO $credentials): auth\src\api\dto\auth\AuthDTO;
    public function refresh(auth\src\api\dto\auth\AuthDTO $authDTO): auth\src\api\dto\auth\AuthDTO;
    public function getSignedInUser(string $token): auth\src\api\dto\auth\AuthDTO;
}