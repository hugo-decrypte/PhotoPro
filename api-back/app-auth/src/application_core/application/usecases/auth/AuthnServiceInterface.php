<?php

namespace toubilib\core\application\usecases\auth;

use auth\src\api\dto\auth\AuthDTO;
use auth\src\api\dto\auth\CredentialsDTO;

interface AuthnServiceInterface{

    public function createUser(auth\src\api\dto\auth\CredentialsDTO $credentials, int $role): string;

    public function byCredentials(auth\src\api\dto\auth\CredentialsDTO $credentials): auth\src\api\dto\auth\AuthDTO;

}