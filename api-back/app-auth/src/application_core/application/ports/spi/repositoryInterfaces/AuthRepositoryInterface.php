<?php

namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use auth\src\application_core\domain\entities\user\User;

interface AuthRepositoryInterface
{

    public function getByEmail($email): User;
    public function saveUser(User $user): void;
    public function deleteUser(User $user): void;

}