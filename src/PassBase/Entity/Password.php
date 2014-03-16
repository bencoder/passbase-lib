<?php
namespace PassBase\Entity;

/**
 * Stores the encrypted password data for a specified site within a password group
 */
interface Password
{
    /**
     * Password ID used for updating/deleting the password
     */
    public function getId();

    /**
     * Returns the passwordGroup this belonds to
     * @return PasswordGroup
     */
    public function getPasswordGroup();

    /**
     * @return string Encrypted json string of the username/password/url/notes
     */
    public function getData();

    /**
     * @return DateTime get the last time this was updated. Used for OCC
     */
    public function getLastUpdatedAt();
}
