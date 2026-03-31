<?php

namespace toubilib\core\application\usecases\auth;

use toubilib\api\dto\auth\AuthDTO;
use toubilib\api\dto\auth\CredentialsDTO;

interface AuthnServiceInterface{

    public function createUser(CredentialsDTO $credentials, int $role): string;

    public function byCredentials(CredentialsDTO $credentials): AuthDTO;

}