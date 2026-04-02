<?php

namespace photopro\core\domain\entities\user;


use Exception;

class User {

    private string $id;
    private string $email;
    private string $password;
    private int $role;

    public function __construct(string $id, string $email, string $password, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['email'],
            $data['password'],
            $data['role']
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