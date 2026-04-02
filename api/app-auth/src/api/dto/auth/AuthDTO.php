<?php

namespace photopro\api\dto\auth;

use http\Encoding\Stream\Inflate;

class AuthDTO {

    private string $id;
    private string $email;
    private string $refreshToken;
    private string $accessToken;

    /**
     * @param string $id
     * @param string $email
     */
    public function __construct(string $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }



    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }



    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

}
