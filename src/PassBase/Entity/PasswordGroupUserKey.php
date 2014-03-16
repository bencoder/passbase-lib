<?php
namespace PassBase\Entity;

/**
 * Stores the encrypted AES key for the passwords in the specified group
 * The keys are encrypted by the User's password. Adding a user to a group requires decrypting the keys with your own
 * password and re-encrypting them with the User's password
 */
interface PasswordGroupUserKey
{

    /**
     * Returns the PasswordGroup that this key belongs to
     * @return PasswordGroup
     */
    public function getPasswordGroup();

    /**
     * Returns the encrypted key for all the passwords in the group.
     * This should be encrypted by the User's plaintext password
     * @return string
     */
    public function getKey();
}
