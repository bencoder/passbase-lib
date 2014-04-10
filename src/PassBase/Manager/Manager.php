<?php
namespace PassBase\Manager;

use PassBase\Encoder\Encoder;
use PassBase\Entity\DecryptedPassword;
use PassBase\Entity\Password;
use PassBase\Entity\PasswordGroup;
use PassBase\Entity\Serializer\DecryptedPasswordSerializer;
use PassBase\Entity\User;
use PassBase\Storage\PasswordGroupUserKeyStorage;
use PassBase\Storage\PasswordStorage;

/**
 * Class to handle Retrieving, Creating and Updating passwords and assigning users to groups
 */
class Manager
{
    /** @var DecryptedPasswordSerializer */
    private $serializer;

    /** @var Encoder */
    private $encoder;

    /** @var PasswordGroupUserKeyStorage */
    private $passwordGroupUserKeyStorage;

    /** @var PasswordStorage */
    private $passwordStorage;

    /**
     * @param DecryptedPasswordSerializer $serializer,
     * @param Encoder $encoder,
     * @param PasswordGroupUserKeyStorage $passwordGroupUserKeyStorage,
     * @param PasswordStorage $passwordStorage
     */
    public function __construct(
        DecryptedPasswordSerializer $serializer,
        Encoder $encoder,
        PasswordGroupUserKeyStorage $passwordGroupUserKeyStorage,
        PasswordStorage $passwordStorage
    ) {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->passwordGroupUserKeyStorage = $passwordGroupUserKeyStorage;
        $this->passwordStorage = $passwordStorage;
    }

    /**
     * Creates the first password for the password group by generating a random key for the group,
     * encrypting it with the User's password and storing it
     *
     * @param PasswordGroup $group
     * @param User $user
     * @param string $userPassword
     */
    public function initialisePasswordGroup(PasswordGroup $group, User $user, $userPassword)
    {
        $unencryptedKey = openssl_random_pseudo_bytes(32);
        $encryptedKey = $this->encoder->encode($userPassword, $unencryptedKey);
        $this->passwordGroupUserKeyStorage->createKeyForUserAndGroup($user, $group, $encryptedKey);
    }

    /**
     * Allows the $targetUser to access the passwords stored in $group
     * Works by decrypting the group key using the sourcePassword, and re-encrypting
     * it using the targetPassword
     *
     * @param User $sourceUser A user who has access to the group
     * @param string $sourcePassword The password for the source user
     * @param User $targetUser The user to add to the group
     * @param string $taretPassword The password of the target user
     * @param PasswordGroup $group The group
     */
    public function assignUserToGroup(
            User $sourceUser,
            $sourcePassword,
            User $targetUser,
            $targetPassword,
            PasswordGroup $group
    ) {
        $sourceKey = $this->passwordGroupUserKeyStorage->getKeyForUserAndGroup($sourceUser, $group);
        $decryptedKey = $this->encoder->decode($sourcePassword, $sourceKey->getKey());
        $encryptedKey = $this->encoder->encode($targetPassword, $decryptedKey);
        $this->passwordGroupUserKeyStorage->createKeyForUserAndGroup($targetUser, $group, $encryptedKey);
    }

    /**
     * Returns all the DecryptedPassword objects for the given user
     * @param User
     * @param string The unhashed password. Required for decrypting
     *
     * @return DecryptedPassword[] indexed by the ID of the password
     */
    public function getPasswordsForUser(User $user, $password)
    {
        $results = [];
        foreach($user->getKeys() as $key) {
            $decryptedKey = $this->encoder->decode($password, $key->getKey());
            $group = $key->getPasswordGroup();
            foreach($group->getPasswords() as $password) {
                $decryptedData = $this->encoder->decode($decryptedKey, $password->getData());
                $results[$password->getId()] = $this->serializer->deserializePassword($decryptedData);
            }
        }

        return $results;
    }

    /**
     * Saves a new password using the Password Storage
     *
     * @param User $user
     * @param PasswordGroup $passwordGroup
     * @param DecryptedPassword $decryptedPassword
     * @param string $userPassword
     */
    public function createPassword(
        User $user,
        PasswordGroup $passwordGroup,
        DecryptedPassword $decryptedPassword,
        $userPassword
    ) {
        $key = $this->passwordGroupUserKeyStorage->getKeyForUserAndGroup($user, $passwordGroup);
        $decryptedKey = $this->encoder->decode($userPassword, $key->getKey());
        $serializedPassword = $this->serializer->serializePassword($decryptedPassword);
        $encryptedData = $this->encoder->encode($decryptedKey, $serializedPassword);
        $this->passwordStorage->addPassword($passwordGroup, $encryptedData);
    }

    /**
     * Updates a password using the Password Storage
     *
     * @param User $user
     * @param Password $password
     * @param DecryptedPassword $decryptedPassword
     * @param string $userPassword
     */
    public function updatePassword(
        User $user,
        Password $password,
        DecryptedPassword $decryptedPassword,
        $userPassword
    ) {
        $key = $this->passwordGroupUserKeyStorage->getKeyForUserAndGroup(
            $user,
            $password->getPasswordGroup()
        );
        $decryptedKey = $this->encoder->decode($userPassword, $key->getKey());
        $serializedPassword = $this->serializer->serializePassword($decryptedPassword);
        $encryptedData = $this->encoder->encode($decryptedKey, $serializedPassword);
        $this->passwordStorage->updatePassword($password, $encryptedData);
    }
}
