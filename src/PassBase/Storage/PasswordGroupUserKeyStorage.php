<?php
namespace PassBase\Storage;

use PassBase\Entity\PasswordGroup;
use PassBase\Entity\PasswordGroupUserKey;
use PassBase\Entity\User;

interface PasswordGroupUserKeyStorage
{
    /**
     * @param User $user
     * @param PasswordGroup $group
     *
     * @return PasswordGroupUserKey
     */
    public function getKeyForUserAndGroup(
        User $user,
        PasswordGroup $group
    );

    /**
     * @param User $user
     * @param PasswordGroup $group
     * @param $key the key for the password group (encrypted by $user's password) to store
     */
    public function createKeyForUserAndGroup(
        User $user,
        PasswordGroup $group,
        $key
    );
}
