<?php
namespace PassBase\Storage;

interface PasswordStorage
{
    /**
     * @param \PassBase\Entity\PasswordGroup $group
     * @param string $encryptedData
     */
    public function addPassword(
        \PassBase\Entity\PasswordGroup $group,
        $encryptedData
    );

    /**
     * @param \PassBase\Entity\Password $password
     * @param string $encryptedData
     */
    public function updatePassword(
        \PassBase\Entity\Password $password,
        $encryptedData
    );
}
