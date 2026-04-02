<?php

namespace photopro\core\application\usecases\auth;

use photopro\api\dto\auth\AuthDTO;
use photopro\api\dto\auth\CredentialsDTO;

interface AuthnServiceInterface{

    public function createUser(CredentialsDTO $credentials, int $role): string;

    public function byCredentials(CredentialsDTO $credentials): AuthDTO;

}