<?php

namespace photopro\core\domain\entities\user;


use Exception;

class User {

    private string $id;
    private string $email;
    private string $password;
    public string $firstName = '';
    public string $name = '';
    public string $pseudo = '';

    public function __construct(string $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['email'],
            $data['password']
        );
    }

    /**
     * @throws Exception
     */
    public function __get(string $name){
        if(property_exists($this,$name)) {
            return $this->$name;
        }
        throw new Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }

}