<?php

namespace toubilib\api\dto\auth;

use http\Encoding\Stream\Inflate;

class AuthDTO {

    private string $id;
    private string $email;
    private int $role;
    private string $refreshToken;
    private string $accessToken;

    /**
     * @param string $id
     * @param string $email
     * @param int $role
     */
    public function __construct(string $id, string $email, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
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

    public function getRole(): int
    {
        return $this->role;
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
