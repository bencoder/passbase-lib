<?php
namespace PassBase\Manager;

/**
 * Class to handle retrieving, Creating and updating passwords
 */
class Manager
{
    /** @var \PassBase\Entity\Serializer\DecryptedPasswordSerializer */
    private $serializer;

    /** @var \PassBase\Encoder\Encoder */
    private $encoder;

    /** @var \PassBase\Storage\PasswordGroupUserKeyStorage */
    private $passwordGroupUserKeyStorage;

    /** @var \PassBase\Storage\PasswordStorage */
    private $passwordStorage;

    /**
     * @param \PassBase\Entity\Serializer\DecryptedPasswordSerializer $serializer,
     * @param \PassBase\Encoder\Encoder $encoder,
     * @param \PassBase\Storage\PasswordGroupUserKeyStorage $passwordGroupUserKeyStorage,
     * @param \PassBase\Storage\PasswordStorage $passwordStorage
     */
    public function __construct(
        \PassBase\Entity\Serializer\DecryptedPasswordSerializer $serializer,
        \PassBase\Encoder\Encoder $encoder,
        \PassBase\Storage\PasswordGroupUserKeyStorage $passwordGroupUserKeyStorage,
        \PassBase\Storage\PasswordStorage $passwordStorage
    ) {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->passwordGroupUserKeyStorage = $passwordGroupUserKeyStorage;
        $this->passwordStorage = $passwordStorage;
    }

    /**
     * Returns all the DecryptedPassword objects for the given user
     * @param \PassBase\Entity\User
     * @param string The unhashed password. Required for decrypting
     *
     * @return \PassBase\Entity\DecryptedPassword[] indexed by the ID of the password
     */
    public function getPasswordsForUser($user, $password)
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
     * @param \PassBase\Entity\User $user
     * @param \PassBase\Entity\PasswordGroup $passwordGroup
     * @param \PassBase\Entity\DecryptedPassword $decryptedPassword
     * @param string $userPassword
     */
    public function createPassword(
        \PassBase\Entity\User $user,
        \PassBase\Entity\PasswordGroup $passwordGroup,
        \PassBase\Entity\DecryptedPassword $decryptedPassword,
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
     * @param \PassBase\Entity\User $user
     * @param \PassBase\Entity\Password $password
     * @param \PassBase\Entity\DecryptedPassword $decryptedPassword
     * @param string $userPassword
     */
    public function updatePassword(
        \PassBase\Entity\User $user,
        \PassBase\Entity\Password $password,
        \PassBase\Entity\DecryptedPassword $decryptedPassword,
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
