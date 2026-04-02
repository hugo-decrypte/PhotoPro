<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\user\User;

interface AuthRepositoryInterface
{

    public function getByEmail($email): User;
    public function saveUser(User $user): void;
    public function deleteUser(User $user): void;

}