<?php
namespace PassBase\Storage;

use PassBase\Entity\Password;
use PassBase\Entity\PasswordGroup;

interface PasswordStorage
{
    /**
     * @param PasswordGroup $group
     * @param string $encryptedData
     */
    public function addPassword(
        PasswordGroup $group,
        $encryptedData
    );

    /**
     * @param Password $password
     * @param string $encryptedData
     */
    public function updatePassword(
        Password $password,
        $encryptedData
    );
}
