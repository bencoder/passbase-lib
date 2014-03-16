<?php
namespace PassBase\Test\Encoder\AES;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->encoder = new \PassBase\Encoder\AES\Encoder();
    }

    public function testEncoder()
    {
        $rawData = "Some raw data to encode and decode";
        $key = "a secret key";
        $encrypted = $this->encoder->encode($key, $rawData);
        $decrypted = $this->encoder->decode($key, $encrypted);
        $this->assertEquals($rawData, $decrypted);
    }
}
