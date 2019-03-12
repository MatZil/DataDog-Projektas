<?php

class User
{
    public $Id;
    public $Email;
    public $PasswordHash;

    public function __construct($id, $email, $passwordHash)
    {
        $this->Id = $id;
        $this->Email = $email;
        $this->PasswordHash = $passwordHash;
    }
}
