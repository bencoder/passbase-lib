<?php
namespace PassBase\Encoder\AES;

use PassBase\Encoder\Encoder as EncoderInterface;

/**
 * AES-256 implementation of the data encoder
 */
class Encoder implements EncoderInterface
{
    /**
     * Encodes the data using AES-256 encryption
     * @see Encoder::encode
     */
    public function encode($key, $data)
    {
        $key = hash('sha256', $key, true);
        $iv = \mcrypt_create_iv(
            mcrypt_get_iv_size(
                MCRYPT_RIJNDAEL_128,
                MCRYPT_MODE_CBC
            ),
            MCRYPT_RAND
        );
        $encrypted = \mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $key,
            $data,
            MCRYPT_MODE_CBC,
            $iv
        );
        return json_encode(array(
            "iv" => base64_encode($iv),
            "data" => base64_encode($encrypted)
        ));
    }

    /**
     * Decodes the data using AES-256 encryption
     * @see Encoder::decode
     */
    public function decode($key, $data)
    {
        $key = hash('sha256', $key, true);
        $data = json_decode($data, true);
        $iv = base64_decode($data['iv']);
        $data = base64_decode($data['data']);
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $key,
            $data,
            MCRYPT_MODE_CBC,
            $iv
        );
        //trim the trailing null bytes. This should only be ascii data so it shouldn't be a problem
        return rtrim($decrypted, "\0");
    }
}
