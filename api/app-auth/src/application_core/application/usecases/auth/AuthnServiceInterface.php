<?php

namespace photopro\core\application\usecases\auth;

use photopro\api\dto\auth\AuthDTO;
use photopro\api\dto\auth\CredentialsDTO;
use photopro\api\dto\auth\UserDTO;

interface AuthnServiceInterface{

    public function createUser(CredentialsDTO $credentials): string;

    public function byCredentials(CredentialsDTO $credentials): AuthDTO;

    public function getUser(string $id): UserDTO;

    public function changePassword(string $userId, string $currentPassword, string $newPassword): void;
}