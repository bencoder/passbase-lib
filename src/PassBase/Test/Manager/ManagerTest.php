<?php
namespace PassBase\Test\Manager;

use \Mockery as M;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->serializer = M::mock('\PassBase\Entity\Serializer\DecryptedPasswordSerializer');
        $this->encoder = M::mock('\PassBase\Encoder\Encoder');
        $this->keyStorage = M::mock('\PassBase\Storage\PasswordGroupUserKeyStorage');
        $this->passwordStorage = M::mock('\PassBase\Storage\PasswordStorage');

        $this->manager = new \PassBase\Manager\Manager(
            $this->serializer,
            $this->encoder,
            $this->keyStorage,
            $this->passwordStorage
        );
    }

    public function testInitialisePasswordGroup()
    {
        $group = M::mock('\PassBase\Entity\PasswordGroup');
        $user = M::mock('\PassBase\Entity\User');
        $userPassword = 'userPassword';
        $this->encoder->shouldReceive('encode')->with($userPassword, M::any())->once();
        $this->keyStorage->shouldReceive('createKeyForUserAndGroup')->with($user, $group, M::any())->once();
        $this->manager->initialisePasswordGroup($group, $user, $userPassword);
    }

    public function testAssignUserToGroup()
    {
        $sourceUser = M::mock('\PassBase\Entity\User');
        $sourcePassword = 'source_password';
        $targetUser = M::mock('\PassBase\Entity\User');
        $targetPassword = 'target_password';
        $group = M::mock('\PassBase\Entity\PasswordGroup');

        $encryptedKey = 'encrypted_key';
        $decryptedKey = 'decrypted_key';
        $encryptedKey2 = 'encrypted_key_2';

        $key = M::mock('\PassBase\Entity\PasswordGroupUserKey');
        $key->shouldReceive('getKey')->andReturn($encryptedKey);

        $this->keyStorage->shouldReceive('getKeyForUserAndGroup')->with($sourceUser, $group)->andReturn($key);

        $this->encoder->shouldReceive('decode')->with($sourcePassword, $encryptedKey)->andReturn($decryptedKey);

        $this->encoder->shouldReceive('encode')->with($targetPassword, $decryptedKey)->andReturn($encryptedKey2);

        $this->keyStorage->shouldReceive('createKeyForUserAndGroup')->with($targetUser, $group, $encryptedKey2)->once();

        $this->manager->assignUserToGroup(
            $sourceUser,
            $sourcePassword,
            $targetUser,
            $targetPassword,
            $group
        );
    }


    public function testGetPasswordsForUser()
    {
        $encryptedKey = 'encryptedKey';
        $decryptedKey = 'decryptedKey';
        $encryptedData = 'encryptedData';
        $decryptedData = 'decryptedData';
        $userPassword = 'userPassword';
        $passwordId = 123;

        $passwords = array(
            M::mock('\PassBase\Entity\Password'),
        );
        $passwords[0]->shouldReceive('getData')->andReturn($encryptedData);
        $passwords[0]->shouldReceive('getId')->andReturn($passwordId);

        $group = M::mock('\PassBase\Entity\PasswordGroup');
        $group->shouldreceive('getPasswords')->andReturn($passwords);

        $keys = array(
            M::mock('\PassBase\Entity\PasswordGroupUserKey'),
        );
        $keys[0]->shouldReceive('getKey')->andReturn($encryptedKey);
        $keys[0]->shouldReceive('getPasswordGroup')->andReturn($group);

        $user = M::mock('\PassBase\Entity\User');
        $user->shouldReceive('getKeys')->andReturn($keys);

        $this->encoder->shouldReceive('decode')->with($userPassword, $encryptedKey)->andReturn($decryptedKey);
        $this->encoder->shouldReceive('decode')->with($decryptedKey, $encryptedData)->andReturn($decryptedData);

        $decryptedPassword = M::mock('\PassBase\Entity\DecryptedPassword');
        $this->serializer->shouldReceive('deserializePassword')->with($decryptedData)->andReturn(
            $decryptedPassword
        );

        $results = $this->manager->getPasswordsForUser($user, $userPassword);
        $this->assertTrue(count($results) == 1);
        $this->assertTrue(isset($results[$passwordId]));
        $this->assertEquals($decryptedPassword, $results[$passwordId]);
    }


    public function testCreatePassword()
    {
        $userPassword = "userPassword";
        $encryptedKey = "encryptedKey";
        $decryptedKey = "decryptedKey";
        $serializedPassword = "serializedPassword";
        $encryptedPassword = "encryptedPassword";

        $user = M::mock('\PassBase\Entity\User');
        $passwordGroup = M::mock('\PassBase\Entity\PasswordGroup');
        $decryptedPassword = M::mock('\PassBase\Entity\DecryptedPassword');
        $key = M::mock('\PassBase\Entity\PasswordGroupUserKey');
        $key->shouldReceive('getKey')->andReturn($encryptedKey);

        $this->keyStorage->shouldReceive('getKeyForUserAndGroup')->with(
            $user, $passwordGroup
        )->andReturn($key);


        $this->encoder->shouldReceive('decode')->with($userPassword, $encryptedKey)->andReturn($decryptedKey);

        $this->serializer->shouldReceive('serializePassword')->with($decryptedPassword)->andReturn($serializedPassword);

        $this->encoder->shouldReceive('encode')->with($decryptedKey, $serializedPassword)->andReturn($encryptedPassword);

        $this->passwordStorage->shouldReceive('addPassword')->with($passwordGroup, $encryptedPassword);

        $this->manager->createPassword($user, $passwordGroup, $decryptedPassword, $userPassword);
    }

    public function testUpdatePassword()
    {
        $userPassword = "userPassword";
        $encryptedKey = "encryptedKey";
        $decryptedKey = "decryptedKey";
        $serializedPassword = "serializedPassword";
        $encryptedPassword = "encryptedPassword";

        $user = M::mock('\PassBase\Entity\User');
        $password = M::mock('\PassBase\Entity\Password');
        $decryptedPassword = M::mock('\PassBase\Entity\DecryptedPassword');
        $group = M::mock('\PassBase\Entity\PasswordGroup');
        $password->shouldReceive('getPasswordGroup')->andReturn($group);
        $key = M::mock('\PassBase\Entity\PasswordGroupUserKey');
        $this->keyStorage->shouldReceive('getKeyForUserAndGroup')->andReturn($key);

        $key->shouldReceive('getKey')->andReturn($encryptedKey);

        $this->encoder->shouldReceive('decode')->with($userPassword, $encryptedKey)->andReturn($decryptedKey);

        $this->serializer->shouldReceive('serializePassword')->with($decryptedPassword)->andReturn($serializedPassword);

        $this->encoder->shouldReceive('encode')->with($decryptedKey, $serializedPassword)->andReturn($encryptedPassword);

        $this->passwordStorage->shouldReceive('updatePassword')->with($password, $encryptedPassword);
        $this->manager->updatePassword($user, $password, $decryptedPassword, $userPassword);
    }

}
