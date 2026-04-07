<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

interface ServiceAuthAdaptatorInterface {
    public function getUserEmail(string $id): string;
}