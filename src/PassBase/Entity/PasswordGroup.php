<?php
namespace PassBase\Entity;

/**
 * Stores details of the PasswordGroup
 */
interface PasswordGroup
{
    /**
     * Returns the password entities that belong to this group
     * @return Password[]
     */
    public function getPasswords();
}
