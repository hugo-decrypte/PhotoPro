<?php

namespace photopro\api\dto\auth;

class CredentialsDTO{

    private string $email;
    private string $password;
    private string $firstName;
    private string $name;
    private string $pseudo;

    public function __construct(string $email, string $password, string $firstName = '', string $name = '', string $pseudo = '')
    {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->name = $name;
        $this->pseudo = $pseudo ?: explode('@', $email)[0];
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

}