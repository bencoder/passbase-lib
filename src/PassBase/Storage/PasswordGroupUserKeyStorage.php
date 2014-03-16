<?php
namespace PassBase\Storage;

interface PasswordGroupUserKeyStorage
{
    /**
     * @param \PassBase\Entity\User $user,
     * @param \PassBase\Entity\PasswordGroup $group
     *
     * @return PasswordGroupUserKey
     */
    public function getKeyForUserAndGroup(
        \PassBase\Entity\User $user,
        \PassBase\Entity\PasswordGroup $group
    );
}
