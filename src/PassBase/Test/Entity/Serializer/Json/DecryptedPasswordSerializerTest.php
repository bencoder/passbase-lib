<?php
namespace PassBase\Test\Entity\Serializer\Json;

use \Mockery as M;

class DecryptedPasswordSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->serializer = new \PassBase\Entity\Serializer\Json\DecryptedPasswordSerializer();
    }
    public function testSerializer()
    {
        $url = "url";
        $username = "username";
        $password = "password";
        $notes = "notes";
        $createdAt = new \DateTime();

        $password = new \PassBase\Entity\DecryptedPassword(
            $url,
            $username,
            $password,
            $notes,
            $createdAt
        );

        $serializedData = $this->serializer->serializePassword($password);
        $deserializedPassword = $this->serializer->deserializePassword($serializedData);
        $this->assertEquals($password->getUrl(), $deserializedPassword->getUrl());
        $this->assertEquals($password->getUsername(), $deserializedPassword->getUsername());
        $this->assertEquals($password->getPassword(), $deserializedPassword->getPassword());
        $this->assertEquals($password->getNotes(), $deserializedPassword->getNotes());
        $this->assertEquals($password->getCreatedAt()->format('r'), $deserializedPassword->getCreatedAt()->format('r'));
    }

}
