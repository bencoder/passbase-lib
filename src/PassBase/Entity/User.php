<?php
namespace PassBase\Entity;


interface User
{
    /**
     * Returns the PasswordGroupUserKEy entities for this user
     * @return PasswordGroupUserKey[]
     */
    public function getKeys();
}
